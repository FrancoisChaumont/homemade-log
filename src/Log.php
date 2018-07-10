<?php

namespace FC\HomemadeLog;

/**
 * class to write logs in file
 */
class Log {
/* constants */
    const DATEFORMAT = "Y.m.d";
    const FILEPATH = "./"; // current directory (NEVER EMPTY!!)
    const FILEPREFIX = "log_";
    const FILEEXTENSION = ".log";
    const MAXFILESIZEKB = 0; // size in Kb (1024Kb = 1Mb, 0 = unlimited file size)
    const DISPLAYDATE = true;

/* member variables */
    private $dateFormat;
    private $displayDate;
    private $filePath;
    private $filePrefix;
    private $fileExtension;
    private $maxFileSize;

/* member functions */
    public function getDateFormat(): string { return $this->dateFormat; }
    public function getDisplayDate(): bool { return $this->displayDate; }
    public function getFilePath(): string { return $this->filePath; }
    public function getFilePrefix(): string { return $this->filePrefix; }
    public function getFileExtension(): string { return $this->fileExtension; }
    public function getMaxFileSize(): int { return $this->maxFileSize; }
    public function getMaxFileSizeReadable(): string { return $this->filesizeReadable($this->maxFileSize); }
    
    public function setDateFormat(string $parDateFormat) { $this->dateFormat = $parDateFormat; }
    public function setDisplayDate(bool $parDisplayDate) { $this->displayDate = $parDisplayDate; }
    public function setFilePath(string $parFilePath) { $this->filePath = $parFilePath; }
    public function setFilePrefix(string $parFilePrefix) { $this->filePrefix = $parFilePrefix; }
    public function setFileExtension(string $parFileExtension) { $this->fileExtension = $parFileExtension; }
    public function setMaxFileSize(int $parMaxFileSize) { $this->maxFileSize = $this->kb2bytes($parMaxFileSize); }
    
    public function resetToDefault() { 
        $this->dateFormat = Log::DATEFORMAT;
        $this->displayDate = Log::DISPLAYDATE;
        $this->filePath = Log::FILEPATH;
        $this->filePrefix = Log::FILEPREFIX;
        $this->fileExtension = Log::FILEEXTENSION;
        $this->maxFileSize = $this->kb2bytes(Log::MAXFILESIZEKB);
    }

    /**
     * Constructor: not used outside the class (see below instead)
     */
    private function __construct() { }

    /**
     * Constructor: instantiate a new Log object with default parameters
     *
     * @return Log
     */
    public static function newDefault() {
        $instance = new self();
        $instance->resetToDefault();

        return $instance;
    }

    /**
     * Constructor: instantiate a new Log object with custom parameters
     *
     * @param string $parFilePath path to log file
     * @param string $parFilePrefix log file prefix
     * @param string $parFileExtension file extension
     * @param integer $parMaxFileSizeKb max file size in Kb
     * @param string $parDateFormat date format
     * @param bool $parDisplayDate whether or not to add current date in the file name
     * @return Log
     */
    public static function newCustom(string $parFilePath=Log::FILEPATH, string $parFilePrefix=Log::FILEPREFIX, string $parFileExtension=Log::FILEEXTENSION, int $parMaxFileSizeKb=Log::MAXFILESIZEKB, string $parDateFormat=Log::DATEFORMAT, bool $parDisplayDate=Log::DISPLAYDATE): Log {
        $instance = new self();
        $instance->dateFormat = $parDateFormat;
        $instance->displayDate = $parDisplayDate;
        $instance->filePath = $parFilePath;
        $instance->filePrefix = $parFilePrefix;
        $instance->fileExtension = $parFileExtension;
        $instance->maxFileSize = $instance->kb2bytes($parMaxFileSizeKb);

        return $instance;
    }

    /**
     * Constructor: instantiate a new Log object with default parameters and write log (append to end of file)
     *
     * @param string $logText text to write to log file
     * @return Log
     */
    public static function newQuickWrite(string $logText): Log {
        $instance = Log::newDefault(); // instantiate a new default object
        $instance->write($logText);

        return $instance;
    }

/* methods */
    /**
     * Write string to log file (append to end of file)
     * Return number of characters written on success, false on failure
     *
     * @param string $logText text to write to log file
     * @return int
     */
    public function write(string $logText): int {
        // check if the file needs to be deleted because exceeding max file size
        $this->deleteFileOverMaxSize();
        // returns the number of character written into the file on success, or FALSE on failure
        return file_put_contents($this->filePath(), $logText.PHP_EOL, FILE_APPEND);
    }

    /**
     * Write string to log file (clear file then write)
     * Return number of characters written on success, false on failure
     *
     * @param string $logText text to write to log file
     * @return int
     */
    public function rewrite(string $logText): int {
        // returns the number of character written into the file on success, or FALSE on failure
        return file_put_contents($this->filePath(), $logText.PHP_EOL);
    }

    /**
     * Return log file name with or without date depending on displayDate member
     *
     * @return string
     */
    private function fileName(): string {
        if ($this->displayDate) { return $this->filePrefix.date($this->dateFormat).$this->fileExtension; }
        else { return $this->filePrefix.$this->fileExtension; }
    }

    /**
     * Return log file path (full path + file name)
     *
     * @return string
     */
    public function filePath(): string {
        return $this->filePath.$this->fileName();
    }

    /**
     * Delete a log file that has exceeded the permitted max size
     *
     * @return void
     */
    private function deleteFileOverMaxSize() {
        if ($this->maxFileSize == 0) { return; }

        $dh = dir($this->filePath);
        $file = $this->fileName();

        if (!(file_exists($file))) { return; }

        if (filesize($file) > $this->maxFileSize) { unlink($file); }
    }

    /**
     * Transform file size in a readable format (using Bytes, Kilobytes, Megabytes, Gigabytes, Terabytes, Petabytes)
     * 
     * @param integer $bytes number of bytes
     * @param integer $decimals decimals to display
     * @return string
     */
    private function filesizeReadable(int $bytes, int $decimals = 2): string {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    /**
     * Transform Kb to b
     * Return result in Kb
     *
     * @param integer $sizeInKb size in Kb
     * @return int
     */
    private function kb2bytes(int $sizeInKb): int {
        return $sizeInKb * 1024;
    }

    /**
     * Return readable file size of the log file
     *
     * @return string
     */
    public function getFileSize(): string {
        $file = $this->fileName();

        if (!(file_exists($file))) { return 0; }
        else { return $this->filesizeReadable(filesize($file)); }
    }
}

