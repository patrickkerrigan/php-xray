<?php

declare(strict_types=1);

namespace Pkerrigan\Xray;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 14/05/2018
 */
class SqlSegment extends RemoteSegment
{
    protected ?string $url = null;

    protected ?string $preparation = null;

    protected ?string $databaseType = null;

    protected ?string $databaseVersion = null;

    protected ?string $driverVersion = null;

    protected ?string $user = null;

    protected ?string $query = null;

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setPreparation(string $preparation): self
    {
        $this->preparation = $preparation;

        return $this;
    }

    public function setDatabaseType(string $databaseType): self
    {
        $this->databaseType = $databaseType;

        return $this;
    }

    public function setDatabaseVersion(string $databaseVersion): self
    {
        $this->databaseVersion = $databaseVersion;

        return $this;
    }

    public function setDriverVersion(string $driverVersion): self
    {
        $this->driverVersion = $driverVersion;

        return $this;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
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
