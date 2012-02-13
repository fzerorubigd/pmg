<?php

namespace modules\mafia;

use awesomeircbot\module\Module;
use awesomeircbot\server\Server;
use modules\mafia\MafiaGame;

class MafiaOpt extends Module {

    public static $requiredUserLevel = 7;

    private function showStates() {
        $server = Server::getInstance();
        if (MafiaGame::$SHOW_MAFIA_COUNT)
            $server->message(MafiaGame::$LOBBY_ROOM, "Show identity on day punish is ON (show-mafia 1)");
        else
            $server->message(MafiaGame::$LOBBY_ROOM, "Show identity on day punish is OFF (show-mafia 0)");

        if (MafiaGame::$WON_STATE_NORMAL)
            $server->message(MafiaGame::$LOBBY_ROOM, "Mafia win state is when mafia cnt = ppl cnt (mafia-state 0)");
        else
            $server->message(MafiaGame::$LOBBY_ROOM, "Mafia win state is when ppl cnt = 0 (mafia-state 1)");


        if (MafiaGame::$DEAD_IS_TALKING)
            $server->message(MafiaGame::$LOBBY_ROOM, "Dead people can talk (dead-talk 1)");
        else
            $server->message(MafiaGame::$LOBBY_ROOM, "Dead people can not talk (sorry) (dead-talk 0)");

        $server->message(MafiaGame::$LOBBY_ROOM, sprintf("Day timeout is %d secound (day-time %d)", MafiaGame::$DAY_TIMEOUT, MafiaGame::$DAY_TIMEOUT));
        $server->message(MafiaGame::$LOBBY_ROOM, sprintf("Night timeout is %d secound (night-time %d)", MafiaGame::$NIGHT_TIMEOUT, MafiaGame::$NIGHT_TIMEOUT));
    }

    public function run() {
        $server = Server::getInstance();
        $opt = $this->parameters(1);
        if (!$opt) {
            $this->showStates();
            return;
        }
        $value = $this->parameters(2, true);
        switch (strtoupper($opt)) {
            case "SHOW-MAFIA":
                MafiaGame::$SHOW_MAFIA_COUNT = $value;
                break;
            case "MAFIA-STATE":
                MafiaGame::$WON_STATE_NORMAL = $value;
                break;
            case "DEAD-TALK":
                MafiaGame::$DEAD_IS_TALKING = $value;
                break;
            case "NIGHT-TIME":
                MafiaGame::$NIGHT_TIMEOUT = intval($value);
                if (MafiaGame::$NIGHT_TIMEOUT < 100)
                    MafiaGame::$NIGHT_TIMEOUT = 100;
                break;
            case "DAY-TIME":
                MafiaGame::$DAY_TIMEOUT = intval($value);
                if (MafiaGame::$DAY_TIMEOUT < 100)
                    MafiaGame::$DAY_TIMEOUT = 100;
                break;
        }
    }

}
