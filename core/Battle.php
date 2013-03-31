<?php

/**
 * 
 * 
 * @package   
 * @author Jstar
 * @copyright Jstar
 * @version 2013
 * @access public
 */
class Battle
{
    private $attackers;
    private $defenders;
    private $report;
    private $battleStarted;

    /**
     * Battle::__construct()
     * 
     * @param PlayerGroup $attackers
     * @param PlayerGroup $defenders
     * @return Battle
     */
    public function __construct(PlayerGroup $attackers, PlayerGroup $defenders)
    {
        $this->attackers = $attackers;
        $this->defenders = $defenders;
        $this->battleStarted = false;
        $this->report = new BattleReport();
    }
    /**
     * Battle::startBattle()
     * 
     * @return null
     */
    public function startBattle($debug=false)
    {
        if(!$debug) ob_start();
        $this->battleStarted = true;
        //only for initial fleets presentation
        $round = new Round( $this->attackers, $this->defenders,0);
        $this->report->addRound($round);
        for ($i = 1; $i <= ROUNDS; $i++)
        {
            $att_lose = $this->attackers->isEmpty();
            $deff_lose = $this->defenders->isEmpty();
            //if one of they are empty then battle is ended, so update the status
            if ($att_lose || $deff_lose)
            {
                $this->checkWhoWon($att_lose, $deff_lose);
                if(!$debug) ob_get_clean();
                return;
            }
            //initialize the round
            $round = new Round( $this->attackers, $this->defenders,$i);
            $round->startRound();
            //add the round to the combatReport
            $this->report->addRound($round);
            //if($i==2) die('Round: '.$this->report->getRound(0)->getNumber()); // ERRORE
            //update the attackers and defenders after round
            $this->attackers =  $round->getAttachersAfterRound();
            $this->defenders =  $round->getDefendersAfterRound();
        }
        //check status after all rounds
        $this->checkWhoWon($this->attackers->isEmpty(), $this->defenders->isEmpty());
        if(!$debug) ob_get_clean();
    }
    /**
     * Battle::checkWhoWon()
     * Assign to groups the status win,lose or draw
     * @param boolean $att_lose
     * @param boolean $deff_lose
     * @return null
     */
    private function checkWhoWon($att_lose, $deff_lose)
    {
        if ($att_lose && !$deff_lose)
        {
            $this->attackers->battleResult = BATTLE_LOSE;
            $this->defenders->battleResult = BATTLE_WIN;
        }
        elseif (!$att_lose && $deff_lose)
        {
            $this->attackers->battleResult = BATTLE_WIN;
            $this->defenders->battleResult = BATTLE_LOSE;
        }
        else
        {
            $this->attackers->battleResult = BATTLE_DRAW;
            $this->defenders->battleResult = BATTLE_DRAW;
        }
    }

    /**
     * Battle::__toString()
     * 
     * @return
     */
    public function __toString()
    {
        $string = "<br><b>Results:</b><br>";
        $string .= "<font color=red>attackers:</font><br>";
        $string .= $this->attackers;
        $string .= "<br><br><font color=red>defenders:</font><br>";
        $string .= $this->defenders;
        return $string;
    }
    /**
     * Battle::getReport()
     * Start the battle if not and return the report.
     * @return BattleReport
     */
    public function getReport()
    {
        if (!$this->battleStarted)
        {
            $this->startBattle();
        }
        return $this->report;
    }
}