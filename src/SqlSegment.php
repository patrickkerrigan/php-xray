<?php

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
class SqlSegment extends RemoteSegment
{
    /**
     * @var string|null
     */
    protected $url;
    /**
     * @var string|null
     */
    protected $preparation;
    /**
     * @var string|null
     */
    protected $databaseType;
    /**
     * @var string|null
     */
    protected $databaseVersion;
    /**
     * @var string|null
     */
    protected $driverVersion;
    /**
     * @var string|null
     */
    protected $user;
    /**
     * @var string|null
     */
    protected $query;

    /**
     * @param string $url
     * @return static
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param string $preparation
     * @return static
     */
    public function setPreparation(string $preparation)
    {
        $this->preparation = $preparation;

        return $this;
    }

    /**
     * @param string $databaseType
     * @return static
     */
    public function setDatabaseType(string $databaseType)
    {
        $this->databaseType = $databaseType;

        return $this;
    }

    /**
     * @param null|string $databaseVersion
     * @return static
     */
    public function setDatabaseVersion(string $databaseVersion)
    {
        $this->databaseVersion = $databaseVersion;

        return $this;
    }

    /**
     * @param string $driverVersion
     * @return static
     */
    public function setDriverVersion(string $driverVersion)
    {
        $this->driverVersion = $driverVersion;

        return $this;
    }

    /**
     * @param string $user
     * @return static
     */
    public function setUser(string $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $query
     * @return static
     */
    public function setQuery(string $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        $data['sql'] = array_filter([
            'url' => $this->url,
            'preparation' => $this->preparation,
            'database_type' => $this->databaseType,
            'database_version' => $this->databaseVersion,
            'driver_version' => $this->driverVersion,
            'user' => $this->user,
            'sanitized_query' => $this->query
        ]);

        return $data;
    }
}
