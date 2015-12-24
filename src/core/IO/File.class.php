<?php

namespace Core\IO;

/**
 * File operations class.
 *
 * @author Miljan Pantic
 */
class File {
        
    /**
     * Retreive file list from the specified path, according to provided pattern.
     * 
     * @param string $path
     * @param string $pattern
     * @param bool $recursive
     * @param const $patternOptions From RecursiveRegexIterator consts
     * @return array List of file paths
     */
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
