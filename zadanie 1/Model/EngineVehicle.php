<?php
declare(strict_types=1);

require("CarElement.php");
trait TColor {
    protected $color;
    public function setColor(string $color): void
    {
        $this->color = $color;
    }
    public function getColor(): string
    {
        return $this->color;
    }
}
trait TName {
    protected $name;
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getName(): string
    {
        return $this->name;
    }
}

interface IEngineVehicle {
    public function drive(string $location, int $distance) : bool;

    public function mountEngine(Engine $engine) : bool;
    public function unmountEngine() : bool;

    public function mountTire(Tire $tire) : bool;
    public function unmountTire(Tire $tire) : bool;

    public function mountDoor(Door $door) : bool;
    public function unmountDoor(Door $door) : bool;
}

abstract class EngineVehicle implements IEngineVehicle {
    use TColor, TName;

    protected $distance;
    protected $location;
    protected $fuelMax;
    protected $fuelLeft;
    protected $fuelBurning;
    // silnik
    protected $engine;
    // kola
    protected $tireCount;
    protected $_tires = [];
    // drzwi
    protected $doorCount;
    protected $_doors = [];

    // distance
    public function getDistance() : int {
        return $this->distance;
    }

    public function drive(string $location, int $distance) : bool {
        if($this->engine != null && count($this->_tires) == $this->tireCount) {
            $neededFuel = $this->calculateNeededFuel($distance);
            if($neededFuel <= $this->fuelLeft) {
                $this->setLocation($location);
                $this->setFuelLeft($distance);
                $this->distance += $distance;

                $this->engine->use($distance);
                foreach ($this->_tires as $tire) {
                    $tire->use($distance);
                }
                return true;
            }
            return false;
        }
        return false;
    }

    // location
    private function setLocation(string $location) {
        $this->location = $location;
    }
    public function getLocation(): string {
        return $this->location;
    }
    
    //fuel
    private function calculateNeededFuel(int $distance) : float {
        $neededFuel = ($distance * $this->fuelBurning) / 100;
        return $neededFuel;
    }
    private function setFuelLeft(int $distance) : void {
        $usedFuel = $this->calculateNeededFuel($distance);
        $this->fuelLeft -= $usedFuel;
    }

    public function tankFuel(): void {
        $this->fuelLeft = $this->fuelMax;
    }

    // engine
    public function mountEngine(Engine $engine) : bool {
        if($this->engine != null) {
            return false;
        }
        if($engine->mount()) {
            $this->engine = $engine;
            return true;
        }
        return false;
    }

    public function unmountEngine() : bool {
        if($this->engine == null) {
            return false;
        }
        $this->engine->unmount();
        $this->engine = null;
        return true;
    }
    public function getEngineInfo() : string {
        return $this->engine->toString();
    }

    // tires
    public function mountTire(Tire $tire): bool {
        if($this->tireCount > count($this->_tires) && $tire->mount()) {
            array_push($this->_tires, $tire);
            return true;
        }
        return false;
    }

    public function unmountTire(Tire $tire) : bool {
        if(count($this->_tires) > 0) {
            foreach ($this->_tires as $tempTireKey => $tempTire) {
                if($tire === $tempTire) {
                    $tire->unmount();
                    unset($this->_tires[$tempTireKey]);
                    return true;
                }
            }
        }
        return false;
        
    }
    public function pompAllTires(): void
    {
        foreach ($this->_tires as $tire) {
            $tire->pomp();
        }
    }

    // drzwi
    public function mountDoor(Door $door): bool {
        if($this->doorCount > count($this->_doors) && $door->mount()) {
            array_push($this->_doors, $door);
            return true;
        }
        return false;
    }

    public function unmountDoor(Door $door) : bool {
        if(count($this->_doors) > 0) {
            foreach ($this->_doors as $tempDoorKey => $tempDoor) {
                if($door === $tempDoor) {
                    $door->unmount();
                    unset($this->_doors[$tempDoorKey]);
                    return true;
                }
            }
        }
        return false;
        
    }
    public function showDoors(): void
    {
        foreach ($this->_doors as $door) {
            echo $door->toString();
        }
    }
}

abstract class Car extends EngineVehicle
{
    protected $doorCount;

    public function toString(): void
    {
        echo "Name: {$this->name}, Location: {$this->getLocation()}, Color: {$this->color}\nDoorCount: {$this->doorCount}, Distance: {$this->getDistance()}\nMax fuel: {$this->fuelMax}, Fuel left: {$this->fuelLeft}\n";
    }
    
    public function getFullReport(): void
    {
        echo "\n-----------RAPORT AUTA------------\n";
        $this->toString();
        echo "SILNIK:\n";
        if($this->engine != null) echo $this->engine->toString();
        else echo "Silnik niezamontowany\n";
        echo "KOLA:\n";
        if(count($this->_tires) > 0) {
            foreach ($this->_tires as $tire) {
                echo $tire->toString();
            }
        } else {
            echo "Kola niezamontowane\n";
        }
        echo "DRZWI:\n";
        if(count($this->_doors) > 0) {
            foreach ($this->_doors as $door) {
                echo $door->toString();
            }
        } else {
            echo "Kola niezamontowane\n";
        }
    }
}

class AudiCar extends Car {
    public function __construct(string $name = "TT", string $color = "Black", string $location = "Unknown", int $doorCount = 4, int $fuelMax = 40)
    {
        // podstawowe
        $this->distance = 0;
        $this->fuelBurning = 10;
        $this->name = $name;
        $this->color = $color;
        $this->location = $location;
        $this->doorCount = $doorCount;
        $this->fuelMax = $fuelMax;
        $this->fuelLeft = $fuelMax;
        // elementy
        $this->engine = null;
        $this->tireCount = 4;
        $this->_tires = [];
        $this->doorCount = $doorCount;
        $this->_doors = [];
    }
}
