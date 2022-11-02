<?php

namespace App;

use Exception;

class Arena 
{
    private array $monsters;
    private Hero $hero;

    private int $size = 10;

    public function __construct(Hero $hero, array $monsters)
    {
        $this->hero = $hero;
        $this->monsters = $monsters;
    }

    public function getDistance(Fighter $startFighter, Fighter $endFighter): float
    {
        $Xdistance = $endFighter->getX() - $startFighter->getX();
        $Ydistance = $endFighter->getY() - $startFighter->getY();
        return sqrt($Xdistance ** 2 + $Ydistance ** 2);
    }

    public function touchable(Fighter $attacker, Fighter $defenser): bool 
    {
        return $this->getDistance($attacker, $defenser) <= $attacker->getRange();
    }

    /**
     * Get the value of monsters
     */ 
    public function getMonsters(): array
    {
        return $this->monsters;
    }

    /**
     * Set the value of monsters
     *
     */ 
    public function setMonsters($monsters): void
    {
        $this->monsters = $monsters;
    }

    /**
     * Get the value of hero
     */ 
    public function getHero(): Hero
    {
        return $this->hero;
    }

    /**
     * Set the value of hero
     */ 
    public function setHero($hero): void
    {
        $this->hero = $hero;
    }

    /**
     * Get the value of size
     */ 
    public function getSize(): int
    {
        return $this->size;
    }

    public function move(Fighter $fighter, string $direction):void
    {
        $x = $fighter->getX();
        $y = $fighter->getY();

        switch($direction){
            case "N" :
                $y++;
                break;
            case "S" :
                $y--;
                break;
            case "E" :
                $x++;
                break;
            case "W" :
                $x--;
                break;
            default ;
        }

        if($y > -1 && $y < $this->getSize() && $x > -1 && $x < $this->getSize()){
            foreach($this->getMonsters() as $monster){
                if($x === $monster->getX() && $y === $monster->getY()){
                    throw new Exception("This place has already been taken");
                }
            }
        }else{
            throw new Exception("You're about to leave Arena!");
        }

        $fighter->setX($x);
        $fighter->setY($y);

    }

    public function battle(int $id)
    {
        $monsters = $this->getMonsters();
        $monsterTargeted = $monsters[$id];

        if($this->touchable($this->hero, $monsterTargeted)){
            $this->hero->fight($monsterTargeted);
            if(!$monsterTargeted->isAlive()){
                $xpWon = $monsterTargeted->getExperience();
                $this->hero->setExperience($this->hero->getExperience() + $xpWon);
                unset($monsters[$id]);
                $this->setMonsters($monsters);
            }
        } else{
            throw new Exception("Too far");
        }


        if($monsterTargeted->isAlive()){
            if($this->touchable($monsterTargeted, $this->hero)){
                $monsterTargeted->fight($this->hero);
            } else{
                throw new Exception("Your enemy is too far to reach you");
            }
        }
    }
}