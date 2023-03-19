<?php

namespace JNPC\settings;

use Closure;
use pocketmine\entity\Location;

class AttributeSettings
{

    private string $networkId;

    private string $behaviorId = '';

    private bool $customEntity = false;

    private Location $location;

    private float $scale = 1.0;

    private float $boundingBoxHeight = 1.8;

    private ?Closure $controller = null;

    private bool $keepLooking = false;

    public static function make(): AttributeSettings
    {
        return new AttributeSettings();
    }

    public function getNetworkId(): string
    {
        return $this->networkId;
    }

    public function networkId(string $networkId): self
    {
        $this->networkId = $networkId;
        return $this;
    }

    public function getBehaviorId(): string
    {
        return $this->behaviorId;
    }

    public function behaviorId(string $behaviorId): self
    {
        $this->behaviorId = $behaviorId;
        return $this;
    }

    public function isCustomEntity(): bool
    {
        return $this->customEntity;
    }

    public function customEntity(bool $customEntity): self
    {
        $this->customEntity = $customEntity;
        return $this;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function location(Location $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getScale(): float
    {
        return $this->scale;
    }

    public function scale(float $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function getBoundingBoxHeight(): float
    {
        return $this->boundingBoxHeight;
    }

    public function boundingBoxHeight(float $boundingBoxHeight): self
    {
        $this->boundingBoxHeight = $boundingBoxHeight;
        return $this;
    }

    public function getController(): ?Closure
    {
        return $this->controller;
    }

    public function controller(?Closure $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    public function isKeepLooking(): bool
    {
        return $this->keepLooking;
    }

    public function keepLooking(bool $keepLooking): self
    {
        $this->keepLooking = $keepLooking;
        return $this;
    }

}