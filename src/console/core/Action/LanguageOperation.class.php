<?php
/**
 * Created by PhpStorm.
 * User: Miljan
 * Date: 2/25/2016
 * Time: 4:01 AM
 */

namespace Console\Core\Action;


class LanguageOperation extends Operation
{

    /**
     * Should display the value if preview_value is passed.
     *
     * @return mixed
     */
    protected function previewValue()
    {
        if ($this->value != $this->previewValueValue)
            return false;

        $availableLanguages = $this->config->get('available_langs');
        $disabledLanguages = $this->config->get('disabled_langs');
        $defaultLanguage = $this->config->get('default_language');

        $str = 'There is no defined languages.' . $this->dnl;

        if (!empty($availableLanguages))
            $str = 'Currently available languages are: ' . implode(', ', $availableLanguages) . $this->dnl;

        if (empty($disabledLanguages))
            $str .= 'No languages are disabled.' . $this->dnl;
        else
            $str .= 'Next languages are disabled: ' . implode(', ', $disabledLanguages) . $this->dnl;

        $str .= 'Default language is: ' . $defaultLanguage;

        return $str;
    }

    /**
     * Performs operation.
     *
     * @return string Operation output for printing.
     */
    public function perform()
    {
        $previewValue = $this->previewValue();

        if ($previewValue !== false)
            return $previewValue;

        $result = 'Nothing happened.';

        $question = 'Enter the two letters that marks the language (e.g. en)';

        $answer = $this->scriptParams->askForUserInput($question, array(), 'language-mark');

        if (strlen($answer) != 2)
            throw new \Exception('Language must be two letter mark.');

        $availableLanguages = $this->config->get('available_langs');

        if ($this->value == 'add')
        {
            if (in_array($answer, $availableLanguages))
                throw new \Exception("Language $answer is already present.");
        }
        else if (!in_array($answer, $availableLanguages))
        {
            throw new \Exception("Language $answer does not exists.");
        }

        switch ($this->value)
        {
            case 'add':
                $result = $this->addLanguage($answer, $availableLanguages);
                break;
            case 'remove':
                $result = $this->removeLanguage($answer, $availableLanguages);
                break;
            case 'disable':
                $result = $this->disableEnableLanguage(true, $answer, $availableLanguages);
                break;
            case 'enable':
                $result = $this->disableEnableLanguage(false, $answer, $availableLanguages);
                break;
            case 'set_default':
                $result = $this->setDefaultLanguage($answer);
                break;
        }

        return $result;
    }

    private function addLanguage($answer, $availableLanguages)
    {
        $filesToCreate = array(
            $this->config->getParsed('lang_dir') . "$answer/$answer.php",
            $this->config->getParsed('lang_dir') . "$answer/{$answer}_pages.php",
        );

        $outputComponents = $this->config->get('output_components');
        $outComponentDir = $this->config->get('output_components_dir');

        foreach ($outputComponents as $component => $componentOptions)
            array_push(
                $filesToCreate,
                $outComponentDir . "{$component}/lang/{$answer}/{$answer}.php"
            );

        foreach($filesToCreate as $file)
            $this->createPhpFile($file);

        array_push($availableLanguages, $answer);

        $this->config->set(
            'available_langs',
            $availableLanguages
        );

        return 'Language added successfully.';
    }

    private function removeLanguage($answer, $availableLanguages)
    {
        $defaultLanguage = $this->config->get('default_language');

        if ($answer == $defaultLanguage)
            throw new \Exception("Language $answer is default language. Please set default language to another language before deletion.");

        $areYouSure = "Are you sure that you want to delete language $answer (yes|no)?";

        $sure = $this->scriptParams->askYesNo($areYouSure);

        if ($sure == 'no')
            return 'Giving up on removing language.';

        $dirsToDelete = array(
            $this->config->getParsed('lang_dir') . "$answer/"
        );

        $outputComponents = $this->config->get('output_components');
        $outComponentDir = $this->config->get('output_components_dir');

        foreach ($outputComponents as $component => $componentOptions)
            array_push(
                $dirsToDelete,
                $outComponentDir . "{$component}/lang/{$answer}/"
            );

        foreach($dirsToDelete as $file)
            $this->deleteDirectory($file);

        if(($key = array_search($answer, $availableLanguages)) !== false)
        {
            unset($availableLanguages[$key]);
        }

        $this->config->set(
            'available_langs',
            $availableLanguages
        );

        return 'Language removed successfully.';
    }

    private function disableEnableLanguage($disable, $answer)
    {
        $disabledLanguages = $this->config->get('disabled_langs');

        if ($disable)
        {
            if (in_array($answer, $disabledLanguages))
                throw new \Exception("Language $answer is already disabled");

            array_push($disabledLanguages, $answer);
        }
        else
        {
            if (!in_array($answer, $disabledLanguages))
                throw new \Exception("Language $answer is already enabled");

            if(($key = array_search($answer, $disabledLanguages)) !== false)
            {
                unset($disabledLanguages[$key]);
            }
        }

        $this->config->set(
            'disabled_langs',
            $disabledLanguages
        );

        $word = ($disable) ? 'disabled' : 'enabled';

        return "Language $answer $word successfully.";
    }

    private function setDefaultLanguage($answer)
    {
        $this->config->set(
            'default_language',
            $answer
        );

        return "Language $answer has been set as default language.";
    }
}