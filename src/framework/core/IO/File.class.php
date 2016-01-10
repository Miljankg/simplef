<?php

namespace Core\IO;

/**
 * File operations class.
 *
 * @author Miljan Pantic
 */
class File {
    
    /* Interface functions */
    
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
    
    /**
     * Writes or appends text to a file.
     * 
     * @param string $file File to write to.
     * @param string $text Text to write.
     * @param bool $append Should content be appended or written.
     * @throws \Exception In case when $file param is empty or null.
     */
    public static function writeToFile($file, $text, $append = false) {
        
        if (empty($file)) {
            
            throw new \Exception("File cannot be empty or null.");
            
        }
        
        $options = null;
        
        if ($append) {
            
            $options = FILE_APPEND;
            
        }
        
        file_put_contents($file, $text, $options);
        
    }
    
    /***********************/    
    
}
