<?php
class APCStats {

    private $fileCacheInfo;
    private $userCacheInfo;
    private $memoryInfo;

    function __construct() {
        $this->checkEnvironment();
        $this->setFileCacheInfo();
        $this->setUserCacheInfo();
        $this->setMemoryInfo();
    }

    protected function checkEnvironment() {
        if(!function_exists('apc_cache_info') || !($this->getFileCacheInfo())) {
            throw new Exception('No cache info available. APC does not appear to be running or installed', 1);
        }
    }

    protected function setFileCacheInfo() {
        $this->fileCacheInfo = new ArrayObject(apc_cache_info('opcode', 1));
    }

    protected function setUserCacheInfo() {
        $this->userCacheInfo = new ArrayObject(apc_cache_info('user', 1));
    }

    protected function setMemoryInfo() {
        $this->memoryInfo = new ArrayObject(apc_sma_info());
    }

    public function getFileCacheInfo() {
        return $this->fileCacheInfo;
    }

    public function getUserCacheInfo() {
        return $this->userCacheInfo;
    }

    public function getMemoryInfo() {
        return $this->memoryInfo;
    }

    public function getSegmentSize() {
        return $this->getMemoryInfo()->offsetGet('seg_size');
    }

    public function getNumberOfSegments() {
        return $this->getMemoryInfo()->offsetGet('num_seg');
    }

    public function getMemoryType() {
        return $this->getFileCacheInfo()->offsetGet('memory_type');
    }

    public function getLockingType() {
        return $this->getFileCacheInfo()->offsetGet('locking_type');
    }

    public function getFileUploadSupport() {
        return $this->getFileCacheInfo()->offsetGet('file_upload_progress');
    }

    public function getApcSettings() {
        return new ArrayObject(ini_get_all('apc'));
    }

    public function getTotalMemory() {
        return $this->getSegmentSize() * $this->getNumberOfSegments();
    }

    public function getAvailableMemory() {
        return $this->getMemoryInfo()->offsetGet('avail_mem');
    }

    public function getUsedMemory() {
        return $this->getTotalMemory() - $this->getAvailableMemory();
    }

    public function getCacheStartTime() {
        return $this->getFileCacheInfo()->offsetGet('start_time');
    }

    public function getNow() {
        return time();
    }

    public function getUptime() {
        return $this->getNow() - $this->getCacheStartTime();
    }

    public function getFileHits() {
        return $this->getFileCacheInfo()->offsetGet('num_hits');
    }

    public function getFileMisses() {
        return $this->getFileCacheInfo()->offsetGet('num_misses');
    }

    public function getFileRequestRate() {
        return ($this->getFileHits() + $this->getFileMisses()) / $this->getUptime();
    }

    public function getFileHitRate() {
        return $this->getFileHits() / $this->getUptime();
    }

    public function getFileMissRate() {
        return $this->getFileMisses() / $this->getUptime();
    }

    public function getFileInserts() {
        return $this->getFileCacheInfo()->offsetGet('num_inserts');
    }

    public function getFileInsertRate() {
        return $this->getFileInserts() / $this->getUptime();
    }

    public function getUserHits() {
        return $this->getUserCacheInfo()->offsetGet('num_hits');
    }

    public function getUserMisses() {
        return $this->getUserCacheInfo()->offsetGet('num_misses');
    }

    public function getUserRequestRate() {
        return ($this->getUserHits() + $this->getUserMisses()) / $this->getUptime();
    }

    public function getUserHitRate() {
        return $this->getUserHits() / $this->getUptime();
    }

    public function getUserMissRate() {
        return $this->getUserMisses() / $this->getUptime();
    }

    public function getUserInserts() {
        return $this->getUserCacheInfo()->offsetGet('num_inserts');
    }

    public function getUserInsertRate() {
        return $this->getUserInserts() / $this->getUptime();
    }

    public function getPhpVersion() {
        return phpversion();
    }

    public function getApcVersion() {
        return phpversion('apc');
    }

    public function getNumberOfFiles() {
        return $this->getFileCacheInfo()->offsetGet('num_entries');
    }

    public function getNumberOfVariables() {
        return $this->getUserCacheInfo()->offsetGet('num_entries');
    }

    public function getSizeOfFiles() {
        return $this->getFileCacheInfo()->offsetGet('mem_size');
    }

    public function getSizeOfVariables() {
        return $this->getUserCacheInfo()->offsetGet('mem_size');
    }

    public function formatBytes ($bytes) {
        $units = array (
            'B',
            'KB',
            'MB',
            'GB',
            'TB'
        );
        $bytes = $bytes * 1024;
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return number_format($bytes, 2) . ' ' . $units[$pow];
    }

}

// Debug
$a = new APCStats();
$methods = get_class_methods($a);
foreach ($methods as $value) {
    var_dump(call_user_func(array($a, $value)));
}
?>
