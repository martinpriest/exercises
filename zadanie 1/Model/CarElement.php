<?php

interface IMountable {
    public function mount() : bool;
    public function unmount() : bool;
}

interface IRepairable {
    public function use(int $distance) : bool;
    public function repair() : void;
}

abstract class CarElement implements IMountable
{
    protected $mounted;
    protected $conditionPercent;

    protected function isMounted() {
        if($this->mounted) return true;
        return false;
    }

    public function mount() : bool {
        if($this->isMounted()) {
            return false;
        }
        $this->mounted = true;
        return true;
    }

    public function unmount() : bool {
        if(!$this->isMounted()) {
            return false;
        }
        $this->mounted = false;
        return true;
    }
}

class Engine extends CarElement implements IRepairable {
    private $type;
    private $power;
    private $capacity;
    private $chiped;

    public function __construct(string $type = "Diesel", float $power = 113, float $capacity = 1.8)
    {
        $this->mounted = false;
        $this->type = $type;
        $this->power = $power;
        $this->capacity = $capacity;
        $this->chiped = false;
        $this->conditionPercent = 100.0;
    }

    // chipowanie
    public function chipEngine(): bool {
        if($this->chiped) return false;
        $this->chiped = true;
        $this->power *= 1.1;
        return true;
    }

    public function unchipEngine(): bool {
        if(!$this->chiped) return false;
        $this->chiped = false;
        $this->power /= 1.1;
        return true;
    }

    public function toString(): string
    {
        if($this->chiped) $chipString = "TAK";
        else $chipString = "NIE";
        if($this->isMounted()) $mString = "TAK";
        else $mString = "NIE";
        return "Silnik {$this->type}, moc: {$this->power} KM, pojemnosc {$this->capacity} L, Czipowany: {$chipString}, Zamontowany: {$mString}, kondycja: {$this->conditionPercent}\n";
    }
    
    // zuzycie
    public function use(int $distance) : bool {
        $dropCondition = $distance/5000;
        if($dropCondition > $this->conditionPercent) return false;

        $this->conditionPercent -= $dropCondition;
        return true;
    }

    public function repair() : void {
        $this->conditionPercent = 100;
    }
}

class Tire extends CarElement {
    private $tireType;
    private $tireSize;
    private $preassureMax;
    private $preassureLeft;
    private $speedIndex;

    public function __construct(string $tireType = "summer", int $tireSize = 195, int $preassureMax = 3, int $speedIndex = 100)
    {
        $this->mounted = false;
        $this->tireType = $tireType;
        $this->tireSize = $tireSize;
        $this->preassureMax = $preassureMax;
        $this->preassureLeft = $preassureMax;
        $this->speedIndex = $speedIndex;
        $this->conditionPercent = 100;
    }

    public function toString(): string
    {
        $this->mounted ? $bool="TAK" : $bool="NIE";
        return "Opona typ: {$this->tireType}, rozmiar {$this->tireSize}, maks cisnienie: {$this->preassureMax}, zostalo cisnienia: {$this->preassureLeft}, zamotnowane: {$bool}, kondycja: {$this->conditionPercent}\n";
    }

    // zuzycie
    public function use(int $distance) : bool {
        $dropCondition = $distance/300;
        $dropPreassure = $distance/2000;
        if($dropCondition > $this->conditionPercent ||
            $dropPreassure > $this->preassureLeft) return false;

        $this->conditionPercent -= $dropCondition;
        $this->preassureLeft -= $dropPreassure;
        return true;
    }

    public function repair() : void {
        $this->conditionPercent = 100;
    }
    
    // pomp tire
    public function pomp(): void
    {
        $this->preassureLeft = $this->preassureMax;
    }
}

class Door extends CarElement {
    private $openState;
    private $glassOpenable;
    private $glassOpenPercent;

    public function __construct(bool $glassOpenable = true) {
        $this->mounted = false;
        $this->openState = false;
        $this->glassOpenable = $glassOpenable;
        $this->glassOpenPercent = 0;
    }

    public function toString(): string
    {
        $this->mounted ? $mountedString="TAK" : $mountedString="NIE";
        $this->openState ? $openStateString="OTWARTE" : $openStateString="ZAMKNIETE";
        $this->glassOpenable ? $glassOpenableState="da sie otworzyc" : $openStateString="nie da sie otworzyc";
        return "Drzwi {$openStateString}, okno {$glassOpenableState}, okno otware w {$this->glassOpenPercent}%, zamontowane: {$mountedString}\n";
    }
}