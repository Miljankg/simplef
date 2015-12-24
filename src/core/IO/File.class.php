<?php

namespace Core\IO;

/**
 * File operations class.
 *
 * @author Miljan Pantic
 */
class File {
        
    public static function getFileList(
            $path, 
            $pattern, 
            $recursive = true, 
            $patternOptions = \RecursiveRegexIterator::GET_MATCH) {
        
        $directoryIterator = new \RecursiveDirectoryIterator($path);    
        
        $iteratorForRegex = $directoryIterator;
        
        if ($recursive) {
            $iteratorForRegex = new \RecursiveIteratorIterator($directoryIterator);
            
        }        
        
        $fileList = new \RegexIterator(
                $iteratorForRegex, 
                $pattern, 
                $patternOptions
                );
        
        $fileListArray = array();
        
        foreach ($fileList as $filePath => $file) {
            
            $fileListArray[] = $filePath;
            
        }
        
        return $fileListArray;
        
    }
    
}
