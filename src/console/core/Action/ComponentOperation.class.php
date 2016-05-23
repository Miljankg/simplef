<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/26/2016
 * Time: 2:43 PM
 */

namespace Console\Core\Action;


abstract class ComponentOperation extends Operation
{
    protected $componentConfigIndex;
    protected $componentType;
    protected $componentConstantPrefix;
    protected $componentTypeName;
    protected $componentDirConfigIndex;
    protected $components = array();
    protected $componentDirectory;
    protected $dependencyType;
    protected $dependencyArray;

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->previewValueValue != $this->value)
            return false;

        $components = $this->components;

        $str = '';

        $componentName = $this->scriptParams->askForUserInput(
            "Please enter $this->componentType component name if you want to filter output (or enter for full report): ",
            array(),
            'component-name'
        );

        if (!empty($componentName))
        {
            $this->checkIfComponentExists($componentName);

            $components = array(
                $componentName => $components[$componentName]
            );
        }

        foreach ($components as $component => $componentDependencies)
        {
            $str .= 'Name: ' . $component . $this->nl;
            $str .= 'Dependencies: [ ' . $this->arrToStr($componentDependencies) . ' ] ' . $this->dnl;
        }

        return $str;
    }

    protected function addComponent($filesToAdd, $name)
    {
        $typeName = ucfirst($this->componentType);
        $components = $this->components;

        if (array_key_exists($name, $components))
            throw new \Exception("$typeName component \"$name\" is already configured.");


        $dependencies = $this->scriptParams->askForUserInput(
            "Please enter $this->componentType component dependencies separated by , (comma) or just press enter for none: ",
            array(),
            'component-dependencies'
        );

        $dependenciesArr = array();

            if (!empty($dependencies))
                $dependenciesArr = explode(',', $dependencies);

        $dependencyConfig = $this->dependencyArray;

        foreach ($dependenciesArr as $key => $dependency)
        {
            if (!isset($dependencyConfig[$dependency]))
                throw new \Exception("Dependency logic component \"{$dependency}\" does not exists.");

            if (in_array($name, $dependencyConfig[$dependency]))
                throw new \Exception("Circular dependency detected $name<=>$dependency.");

            $dependenciesArr[$key] = trim($dependency);
        }

        $components[$name] = $dependenciesArr;

        $this->config->set($this->componentConfigIndex, $components);

        foreach ($filesToAdd as $file => $content)
        {
            $noPhpTag = false;

            if (substr($file, -4) != '.php')
                $noPhpTag = true;

            $this->createPhpFile($file, $content, $noPhpTag);
        }

        define($this->componentConstantPrefix . strtoupper($name), $name);

        return "$typeName component successfully added.";
    }

    protected function removeComponent($name, array $directoriesToRemove, $askSecQuestion = true)
    {
        $components = $this->components;

        if (!array_key_exists($name, $components))
            throw new \Exception("$this->componentTypeName component \"$name\" is not configured.");

        if ($askSecQuestion)
        {
            $areYouSure = "Are you sure that you want to delete $this->componentType component $name (yes|no)?";

            $sure = $this->scriptParams->askYesNo($areYouSure);

            if ($sure == 'no')
                return "Giving up on removing $this->componentType component.";
        }

        unset($components[$name]);

        $this->config->set($this->componentConfigIndex, $components);

        foreach ($directoriesToRemove as $directory)
            $this->deleteDirectory($directory);

        $constant = $this->componentConstantPrefix . strtoupper($name);

        if (defined($constant))
            $this->config->queueConstantForRemoval($constant);

        return "$this->componentTypeName component \"$name\" removed successfully.";
    }

    protected function addDependency($name)
    {
        $components = $this->components;

        $this->checkIfComponentExists($name);

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the logic component to add as dependency:",
            array(),
            'logic-component'
        );

        $this->checkIfComponentExists($dependency, true, 'logic_components');

        if (isset($components[$dependency]) && in_array($name, $components[$dependency]))
            throw new \Exception("Circular dependency detected $name<=>$dependency.");

        if (isset($components[$name]) && in_array($dependency, $components[$name]))
            throw new \Exception("$this->componentTypeName component \"$name\" already has logic component \"$dependency\" as its dependency.");

        array_push($components[$name], $dependency);

        $this->config->set($this->componentConfigIndex, $components);

        return "Logic component \"$dependency\" added as dependency for \"$name\" component.";
    }

    protected function removeDependency($name)
    {
        $components = $this->components;

        $this->checkIfComponentExists($name);

        $dependency = $this->scriptParams->askForUserInput(
            "Enter the name of the logic component to remove as dependency:",
            array(),
            'logic-component'
        );

        $this->checkIfComponentExists($dependency, true, 'logic_components');

        if (!in_array($dependency, $components[$name]))
            throw new \Exception("$this->componentTypeName component \"$name\" does not have logic component \"$dependency\" as its dependency.");

        if(($key = array_search($dependency, $components[$name])) !== false)
        {
            unset($components[$name][$key]);
        }

        $this->config->set($this->componentConfigIndex, $components);

        return "Logic component \"$dependency\" removed as dependency for \"$name\" component.";
    }

    protected abstract function genArrayOfComponentFiles($componentName);
    protected abstract function genArrayOfComponentDirectories($componentName);

    protected function checkIfComponentExists($componentName, $throw = true, $configIndexToCheck = '')
    {
        $components = $this->components;

        if (!empty($configIndexToCheck))
            $components = $this->config->getParsed($configIndexToCheck);

        if (!isset($components[$componentName]))
        {
            if ($throw)
                throw new \Exception(ucfirst($this->componentType) . " component \"$componentName\" does not exists in configured components.");
            else
                return false;
        }

        return true;
    }

    protected function getComponentFilesStructure($componentName, $componentStructure = array(), $componentType = 'logic', $dependencies = array())
    {
        $ocType = $this->consoleConfig['output_component_type'];

        $componentDir = ($componentType == $ocType) ? $this->config->getParsed('output_components_dir') . $componentName . '/' : $this->config->getParsed('logic_components_dir') . $componentName . '/';

        $innerDirRoot = 'backend/' . $componentType . '_' . $componentName . '/';

        $componentStructure[$componentType][$componentName]['files']['config'] = array(
            'file' => $componentName . '_config.php',
            'directory' => $componentDir . 'config/',
            'inner_directory' => $innerDirRoot . 'config/',
            'required' => true
        );
        $componentStructure[$componentType][$componentName]['files']['main_file'] = array(
            'file' => $componentName . '.php',
            'directory' => $componentDir,
            'inner_directory' => $innerDirRoot,
            'required' => true
        );

        if ($componentType == $ocType)
        {
            $langs = $this->config->get('available_langs');

            $templateDir = $this->config->getParsed('templates_dir') . 'out_components/' . $componentName . '/';
            $templateInnerDir = 'frontend/' . $componentType . '_' . $componentName . '/';

            $componentStructure[$componentType][$componentName]['files']['lang_file'] = array(
                'file' => array(),
                'directory' => $componentDir . 'lang/',
                'inner_directory' => $innerDirRoot . 'lang/',
                'required' => true
            );

            foreach ($langs as $lang)
            {
                $componentStructure[$componentType][$componentName]['files']['lang_file']['file'][] = $lang . '/' . $lang . '.php';
            }

            if (file_exists($templateDir . 'css/' . $componentName . '.css'))
                $componentStructure[$componentType][$componentName]['files']['css_file'] = array(
                    'file' => $componentName . '.css',
                    'directory' => $templateDir . 'css/',
                    'inner_directory' => $templateInnerDir . 'css/',
                    'required' => false
                );

            if (file_exists($templateDir . 'js/' . $componentName . '.js'))
                $componentStructure[$componentType][$componentName]['files']['js_file'] = array(
                    'file' => $componentName . '.js',
                    'directory' => $templateDir . 'js/',
                    'inner_directory' => $templateInnerDir . 'js/',
                    'required' => false
                );

            $componentStructure[$componentType][$componentName]['files']['tpl_file'] = array(
                'file' => $componentName . '.tpl',
                'directory' => $templateDir,
                'inner_directory' => $templateInnerDir,
                'required' => true
            );
        }
        else
        {
            $files = glob($componentDir . 'sql/*.sql');

            $fileNameList = array();

            foreach ($files as $file)
            {
                $fileNameList[] = basename($file);
            }

            $componentStructure[$componentType][$componentName]['files']['sql_file'] = array(
                'file' => $fileNameList,
                'directory' => $componentDir . 'sql/',
                'inner_directory' => $innerDirRoot . 'sql/',
                'required' => false
            );
        }

        $componentStructure[$componentType][$componentName]['dependencies'] = $dependencies;

        return $componentStructure;
    }

    protected function generateXmlFile(array $componentFileStructure, $path)
    {
        $writer = new \XMLWriter();
        $writer->openURI($path . '/structure.xml');
        $writer->startDocument('1.0','UTF-8');
        $writer->setIndent(8);
        $writer->startElement('components');
        foreach ($componentFileStructure as $type => $componentsData) {
            foreach ($componentsData as $componentName => $componentData)
            {
                $writer->startElement('component');

                $writer->writeAttribute('name', $componentName);
                $writer->writeAttribute('type', $type);

                $writer->startElement('files');

                foreach ($componentData['files'] as $fileRole => $fileData)
                {
                    $file = $fileData['file'];

                    if (!is_array($file)) {
                        $writer->writeElement($fileRole, $file);
                    } else {
                        $writer->startElement($fileRole);

                        foreach ($file as $fileToWrite) {
                            $writer->writeElement('file', $fileToWrite);
                        }

                        $writer->endElement();
                    }
                }

                $writer->endElement();

                $writer->writeElement('dependencies', implode(',', $componentData['dependencies']));

                $writer->endElement();
            }
        }

        $writer->endElement();
    }

    protected function checkComponentFiles($componentFileStructure)
    {
        foreach ($componentFileStructure as $fileRole => $fileData)
        {
            if (!$fileData['required'])
                continue;

            $fileToCheck = array();
            $file = $fileData['file'];

            if (!is_array($file))
            {
                $fileToCheck[] = $file;
            }
            else
            {
                $fileToCheck = $file;
            }

            foreach ($fileToCheck as $fileName)
            {
                $filePath = $fileData['directory'] . $fileName;

                if (!is_file($filePath))
                    throw new \Exception("File $filePath does not exits.");
            }
        }
    }

    protected function copyComponentFiles($componentFileStructure, $path)
    {
        foreach ($componentFileStructure as $componentType => $typeData)
            foreach ($typeData as $componentName => $componentData)
                foreach ($componentData['files'] as $fileRole => $fileData)
                {
                    $fileToCopy = array();
                    $file = $fileData['file'];

                    if (!is_array($file))
                    {
                        $fileToCopy[] = $file;
                    }
                    else
                    {
                        $fileToCopy = $file;
                    }

                    foreach ($fileToCopy as $fileName)
                    {
                        $dirToCopyTo = dirname($path . $fileData['inner_directory'] . '/' . $fileName);
                        $pathToCopyTo = $dirToCopyTo . '/' . basename($fileName);
                        $filePath = $fileData['directory'] . $fileName;

                        if (!$fileData['required'] && !file_exists($filePath))
                            continue;

                        if(!file_exists($dirToCopyTo))
                            mkdir($dirToCopyTo, 0777, true);

                        if (!copy($filePath, $pathToCopyTo))
                            throw new \Exception("File $filePath cannot be copied to $pathToCopyTo");
                    }
                }
    }

    protected function removeDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object))
                        $this->removeDirectory($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

    protected function zipExport($path)
    {
        // Get real path for our folder
        $rootPath = realpath($path);

        // Initialize archive object
        $zip = new \ZipArchive();

        $openResult = $zip->open($rootPath . '.zip', \ZipArchive::EXCL | \ZipArchive::CREATE);

        if ($openResult === \ZipArchive::ER_EXISTS)
            throw new \Exception("Zip file $rootPath.zip already exists.");
        else if ($openResult !== TRUE)
            throw new \Exception("Error opening $rootPath.zip file.");

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();

        $this->removeDirectory($path);
    }

    protected function exportComponent($componentName, $path, $componentType, &$alreadyExported = array())
    {
        if (in_array($componentType . '_' . $componentName, $alreadyExported))
            return false;

        $alreadyExported[] = $componentType . '_' . $componentName;

        $componentStructure = array();

        $this->checkIfComponentExists($componentName, true, $componentType . '_components');

        $componentData = $this->config->get($componentType . '_components');

        $dependencies = $componentData[$componentName];

        $componentStructure = $this->getComponentFilesStructure($componentName, $componentStructure, $componentType, $dependencies);

        $this->checkComponentFiles($componentStructure[$componentType][$componentName]['files']);

        $tmpComponentStructure = $componentStructure;

        foreach ($tmpComponentStructure[$componentType][$componentName]['dependencies'] as $dependency)
        {
            $newCompStructure = $this->exportComponent($dependency, $path, 'logic', $alreadyExported);

            if ($newCompStructure === false)
                continue;

            $componentStructure = array_merge_recursive($componentStructure, $newCompStructure);
        }

        return $componentStructure;
    }

    protected function exportComponentAndDependencies($componentName)
    {
        $tmpName = $this->componentType . '_component_' . $componentName . '_export';
        $path = $this->scriptParams->askForUserInput('Enter export path: ', array(), 'path') . '/' . $tmpName . '/';

        if (is_dir($path))
            throw new \Exception("Path $path already exists.");

        $componentStructure = $this->exportComponent($componentName, $path, $this->componentType);

        $this->copyComponentFiles($componentStructure, $path);

        $this->generateXmlFile($componentStructure, $path);

        $this->zipExport($path);

        return "Component $componentName exported successfully";
    }

    protected function checkComponentData($componentsData, &$updatedComponents)
    {
        $outputComponentType = $this->consoleConfig['output_component_type'];
        $updatedComponents = array();

        $newComponentsData = array();

        $firstComponent = reset($componentsData);

        if ($firstComponent['type'] !== $this->componentType)
            throw new \Exception("Wrong component type. Expected {$this->componentType}, got {$firstComponent['type']}");

        foreach ($componentsData as $componentName => $componentData)
        {
            $backendDestDir = ($componentData['type'] == $outputComponentType) ? $this->config->getParsed('output_components_dir') : $this->config->getParsed('logic_components_dir');

            foreach($componentData['files'] as $fileRole => $fileData)
            {
                $filePath = $fileData['src_dir'] . $fileData['file_name'];

                if (!file_exists($filePath))
                {
                    throw new \Exception("File $filePath does not exists.");
                }
            }

            foreach ($componentData['dependencies'] as $dependency)
            {
                if (!array_key_exists($dependency, $componentsData))
                    throw new \Exception("Dependency $dependency of component $componentName is not listed in the export XML.");
            }

            $componentsToCheck = $this->config->get($componentData['type'] . '_components');

            $newComponentName = $componentName;

            if (array_key_exists($componentName, $componentsToCheck))
            {
                $newComponentName = $componentName . '_' . rand(1000000, 9999999);

                $updatedComponents[$componentData['type'] . '_' . $componentName] = $newComponentName;

                $componentData['files']['config']['dest_dir'] = $backendDestDir . $newComponentName . '/config/';
                $componentData['files']['main_file']['dest_dir'] = $backendDestDir . $newComponentName . '/';

                if ($componentData['type'] === $outputComponentType)
                {
                    $templateDir = $this->config->getParsed('templates_dir') . 'out_components/';

                    if (isset($componentData['files']['js_file']))
                        $componentData['files']['js_file']['dest_dir'] = $templateDir . $newComponentName . '/js/';
                    if (isset($componentData['files']['css_file']))
                        $componentData['files']['css_file']['dest_dir'] = $templateDir . $newComponentName . '/css/';
                    $componentData['files']['tpl_file']['dest_dir'] = $templateDir . $newComponentName . '/';

                    foreach ($componentData['files'] as $fRole => $fData)
                    {
                        if (substr( $fRole, 0, 5 ) === "lang_")
                            $componentData['files'][$fRole]['dest_dir'] = $backendDestDir . $newComponentName . '/lang/';
                    }
                }
                else
                {
                    foreach ($componentData['files'] as $fRole => $fData)
                    {
                        if (substr( $fRole, 0, 4 ) === "sql_")
                            $componentData['files'][$fRole]['dest_dir'] = $backendDestDir . $newComponentName . '/sql/';
                    }
                }
            }

            $newComponentsData[$newComponentName] = $componentData;
        }

        $finalComponentData = array();

        foreach ($updatedComponents as $key => $newComponentName)
        {
            $tmp = explode('_', $key);

            $componentName = $tmp[1];
            $componentType = $tmp[0];

            if ($componentType === $outputComponentType)
                continue;

            foreach ($newComponentsData as $newComponentNameKey => $newComponentData)
            {
                if (!isset($finalComponentData[$newComponentNameKey]))
                    $finalComponentData[$newComponentNameKey] = $newComponentData;

                $newDependencies = array();

                foreach ($finalComponentData[$newComponentNameKey]['dependencies'] as $dependency)
                {
                    if ($dependency == $componentName)
                    {
                        $newDependencies[] = $newComponentName;
                    }
                    else
                    {
                        $newDependencies[] = $dependency;
                    }
                }

                $finalComponentData[$newComponentNameKey]['dependencies'] = $newDependencies;
            }
        }

        return $finalComponentData;
    }

    protected function readImportXml($tempPath)
    {
        $xmlPath = $tempPath . 'structure.xml';

        $availableLangs = $this->config->get('available_langs');

        $loadedXml = simplexml_load_file($xmlPath);

        $data = array();

        $backendSrcDir = $tempPath . 'backend/';
        $frontendSrcDir = $tempPath . 'frontend/';
        $backendDestDir = $this->config->getParsed('logic_components_dir');

        $ocType = $this->consoleConfig['output_component_type'];

        foreach ($loadedXml as $componentObject)
        {
            $componentName = (string)$componentObject['name'];
            $data[$componentName]['type'] = (string)$componentObject['type'];

            if ($data[$componentName]['type'] == $ocType)
            {
                $backendDestDir = $this->config->getParsed('output_components_dir');
                $templateDir = $this->config->getParsed('templates_dir') . '/out_components/';

                $csFileName = (string)$componentObject->files->css_file;

                if (file_exists($frontendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/css/' . $csFileName))
                {
                    $data[$componentName]['files']['js_file']['file_name'] = (string)$componentObject->files->js_file;
                    $data[$componentName]['files']['js_file']['src_dir'] = $frontendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/css/';
                    $data[$componentName]['files']['js_file']['dest_dir'] = $templateDir . $componentName . '/css/';
                }

                $jsFileName = (string)$componentObject->files->js_file;

                if (file_exists($frontendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/js/' . $jsFileName))
                {
                    $data[$componentName]['files']['css_file']['file_name'] = (string)$componentObject->files->js_file;
                    $data[$componentName]['files']['css_file']['src_dir'] = $frontendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/css/';
                    $data[$componentName]['files']['css_file']['dest_dir'] = $templateDir . $componentName . '/css/';
                }

                $data[$componentName]['files']['tpl_file']['file_name'] = (string)$componentObject->files->tpl_file;
                $data[$componentName]['files']['tpl_file']['src_dir'] = $frontendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/';
                $data[$componentName]['files']['tpl_file']['dest_dir'] = $templateDir . $componentName . '/';

                $langFiles = $componentObject->files->lang_file->file;

                $langs = array();

                foreach ($langFiles as $file)
                {
                    $langs[] = dirname($file);

                    $data[$componentName]['files']['lang_' . dirname($file)]['file_name'] = (string)$file;
                    $data[$componentName]['files']['lang_' . dirname($file)]['src_dir'] = $backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/lang/';
                    $data[$componentName]['files']['lang_' . dirname($file)]['dest_dir'] = $backendDestDir . $componentName . '/lang/';
                }

                $langsToAdd = array_diff($availableLangs, $langs);

                foreach ($langsToAdd as $lang)
                {
                    $this->createPhpFile($backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/lang/' . $lang . '/' . $lang . '.php');

                    $data[$componentName]['files']['lang_' . $lang]['file_name'] =  $lang . '/' . $lang . '.php';
                    $data[$componentName]['files']['lang_' . $lang]['src_dir'] = $backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/lang/';
                    $data[$componentName]['files']['lang_' . $lang]['dest_dir'] = $backendDestDir . $componentName . '/lang/';
                }

            }
            else
            {
                $sqlSrcDir = $backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/sql/';

                if (file_exists($sqlSrcDir))
                {
                    $sqlFiles = glob ( $sqlSrcDir . '*.sql');
                    $i = 0;

                    foreach ($sqlFiles as $sqlFile)
                    {
                        $data[$componentName]['files']['sql_file_' . $i]['file_name'] = basename($sqlFile);
                        $data[$componentName]['files']['sql_file_' . $i]['src_dir'] = $sqlSrcDir;
                        $data[$componentName]['files']['sql_file_' . $i++]['dest_dir'] = $backendDestDir . $componentName . '/sql/';
                    }
                }
            }

            $data[$componentName]['files']['config']['file_name'] = (string)$componentObject->files->config;
            $data[$componentName]['files']['config']['src_dir'] = $backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/config/';
            $data[$componentName]['files']['config']['dest_dir'] = $backendDestDir . $componentName . '/config/';
            $data[$componentName]['files']['main_file']['file_name'] = (string)$componentObject->files->main_file;
            $data[$componentName]['files']['main_file']['src_dir'] = $backendSrcDir . $data[$componentName]['type'] . '_' . $componentName . '/';
            $data[$componentName]['files']['main_file']['dest_dir'] = $backendDestDir . $componentName . '/';

            $dependencies = (string)$componentObject->dependencies;

            $data[$componentName]['dependencies'] = array();

            if (!empty($dependencies))
                $data[$componentName]['dependencies'] = explode(',', $dependencies);
        }

        return $data;
    }

    protected function copyFiles($componentsData)
    {
        foreach ($componentsData as $componentName => $componentData)
        {
            foreach ($componentData['files'] as $fileRole => $fileData)
            {
                $src = $fileData['src_dir'] . $fileData['file_name'];
                $dest = $fileData['dest_dir'] . $fileData['file_name'];

                if(!file_exists(dirname($dest)))
                    mkdir(dirname($dest), 0777, true);

                if (!copy($src, $dest))
                    throw new \Exception("File $src cannot be copied to $dest");
            }
        }
    }

    protected function import()
    {
        $path = $this->scriptParams->askForUserInput("Please enter zip file path: ", array(), 'path');

        if (!file_exists($path))
            throw new \Exception("Invalid zip file path supplied.");

        $tempName = $this->config->getParsed('temp_dir') . rand(1000000, 9999990) . '/';

        if(!file_exists($tempName))
            mkdir($tempName, 0777, true);

        $zip = new \ZipArchive;
        $res = $zip->open($path);

        if ($res === TRUE)
        {
            $zip->extractTo($tempName);
            $zip->close();
        }
        else
        {
            throw new \Exception("Zip file $path cannot be opened.");
        }

        $componentData = $this->readImportXml($tempName);

        $updatedComponents = array();

        $newComponentsData = $this->checkComponentData($componentData, $updatedComponents);

        $this->copyFiles($newComponentsData);

        $this->removeDirectory($tempName);

        $componentsToChange = array();

        $componentsToChange['logic'] = $this->config->get('logic_components');
        $componentsToChange['output'] = $this->config->get('output_components');

        foreach ($newComponentsData as $componentName => $componentData)
        {
            $componentsToChange[$componentData['type']][$componentName] = $componentData['dependencies'];
        }

        $this->config->set('logic_components', $componentsToChange['logic']);
        $this->config->set('output_components', $componentsToChange['output']);

        $str = "";

        foreach ($updatedComponents as $componentNameStr => $newComponentName)
        {
            $tmp = explode('_', $componentNameStr);

            $componentName = $tmp[1];

            $str .= "Component $componentName renamed to $newComponentName\n";
        }

        return $str . "\n" . "Import finished successfully.";
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        // TODO: Implement perform() method.
    }
}