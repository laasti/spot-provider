<?php


namespace Laasti\SpotProvider;

class MonologSqlLogger implements \Doctrine\DBAL\Logging\SQLLogger
{
    protected $logger;
    protected $sql = "";
    protected $start = null;
    
    public function __construct($logger = null) {
        $this->logger = $logger;
   }
    public function startQuery($sql, array $params = null, array $types = null) {
        $this->start = microtime(true);
        $this->sql = preg_replace_callback("/\?/", function($matches) use (&$params, &$types) {
            $param = array_shift($params);
            if (null === $param) {
                return "NULL";
            } else if (is_array($param)) {
                return "'" . implode(', ', $param) . "'";
            } else {
                return "'" . $param . "'";
            }
        }, $sql);
    }
    
    public function stopQuery() {
        $elapsed = microtime(true) - $this->start;
        $this->sql .= " -- {$elapsed}";
        $this->logger->debug($this->sql);
    }
}
