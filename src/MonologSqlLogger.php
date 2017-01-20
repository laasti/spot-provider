<?php

namespace Laasti\SpotProvider;

use Doctrine\DBAL\Logging\SQLLogger;

class MonologSqlLogger implements SQLLogger
{
    protected $logger;
    protected $sql = "";
    protected $start = null;

    /**
     * MonologSqlLogger constructor.
     * @param null $logger
     */
    public function __construct($logger = null)
    {
        $this->logger = $logger;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->start = microtime(true);
        $this->sql = preg_replace_callback("/\?/", function ($matches) use (&$params, &$types) {
            $param = array_shift($params);
            if (null === $param) {
                return "NULL";
            } elseif (is_array($param)) {
                return "'" . implode(', ', $param) . "'";
            } else {
                return "'" . $param . "'";
            }
        }, $sql);
    }

    public function stopQuery()
    {
        $elapsed = microtime(true) - $this->start;
        $this->sql .= " -- {$elapsed}";
        $this->logger->debug($this->sql);
    }
}
