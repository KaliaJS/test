<?php

namespace App\Support;

use DeviceDetector\DeviceDetector as DD;

class DeviceDetector
{
    protected $deviceName;
    protected $deviceFingerprint;

    public function __construct(
        protected DD $detector,
    ) {
        $this->deviceName = $this->createDeviceName();
        $this->deviceFingerprint = $this->createDeviceFingerprint();
    }

    public function createDeviceName()
    {
        return $this->getDeviceBrand() . ' ' .
               $this->getDeviceType() . ' ' .
               $this->getOsName() . ' ' .
               $this->getClientName();
    }

    public function createDeviceFingerprint()
    {
        return hash('crc32b', $this->deviceName);
    }

    public function getClientType()
    {
        return $this->detector->getClient('type');
    }

    public function getClientName()
    {
        return $this->detector->getClient('short_name');
    }

    public function getClientVersion()
    {
        return $this->detector->getClient('version');
    }

    public function getOsName()
    {
        return $this->detector->getOs('short_name');
    }

    public function getOsVersion()
    {
        return $this->detector->getOs('version');
    }

    public function getDeviceName()
    {
        return $this->deviceName;
    }

    public function getDeviceType()
    {
        return $this->detector->getDeviceName();
    }

    public function getDeviceBrand()
    {
        return $this->detector->getBrand();
    }

    public function getDeviceModel()
    {
        return $this->detector->getModel();
    }

    public function getFingerprint()
    {
        return $this->deviceFingerprint;
    }

    public function isWellKnow($devices)
    {
        return $devices->contains('fingerprint', $this->deviceFingerprint);
    }
}
