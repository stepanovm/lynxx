<?php

namespace app\Service;

use Lynxx\Lynxx;

class GothicCodeResolver
{

    private $platesCount;
    private $rulesMatrix;
    private $startPosition;
    private $error = "";
    private $solution;

    private $limit = 0;

    /**
     * @param int $platesCount
     */

    /*
    public function __construct(int $platesCount, array $rulesMatrix, array $startPosition)
    {
        $this->platesCount = $platesCount;
    }
    */

    /**
     * @param array $position
     * @param array $rules
     * @return bool
     */
    public function init(array $position, array $rules)
    {
        $this->platesCount = count($position);
        $this->rulesMatrix = $rules;

        $startPosition = [
            'position' => array_map(function ($elem) {
                return $elem - 1;
            }, $position),
            'history' => [],
            'steps' => 0,
        ];

        $this->startPosition = $startPosition;
    }

    public function resolve()
    {
        $this->doResolve();

        if ($this->error !== "") {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getStartPosition()
    {
        return $this->startPosition;
    }



    public function getError()
    {
        return $this->error;
    }

    public function getSolution()
    {
        $solution = [];
        $step = 0;
        $currentPlate = -1;

        foreach ($this->solution['history'] as $history) {
            if ($history[0] !== $currentPlate) {
                $step++;
                $currentPlate = $history[0];
                $solution[$step] = [
                    'plate' => $currentPlate,
                    'direction' => $history[1] === 1 ? 'Налево' : 'Направо',
                    'count' => 1,
                ];
            } else {
                $solution[$step]['count']++;
            }
        }

        $this->solution['readable'] = $solution;
        $this->solution['message'] = 'Завершено за ' . $this->limit . ' итераций';

        return $this->solution;
    }

    private function getPositionStepsCount($position)
    {
        $step = 0;
        $currentPlate = -1;

        foreach ($position['history'] as $history) {
            if ($history[0] !== $currentPlate) {
                $step++;
                $currentPlate = $history[0];
            }
        }
        return $step;
    }

    private function serializeState(array $state): string
    {
        return implode(',', $state);
    }

    private function doResolve()
    {
        $queue = [];
        $queue[] = $this->getStartPosition();
        $visited = [];
        $visited[$this->serializeState($this->getStartPosition()['position'])] = true;

        while(!empty($queue)) {

            $position = array_shift($queue);

            for ($plateNum = 0; $plateNum < $this->platesCount; $plateNum++) {
                if ($pos = $this->movePlate($position, $plateNum, 1)) {
                    if ($this->isSolved($pos)) {
                        $this->solution = $pos;
                        return;
                    }
                    $key = $this->serializeState($pos['position']);
                    if (!isset($visited[$key])) {
                        $visited[$key] = true;
                        $queue[] = $pos;
                    }
                }
                if ($pos = $this->movePlate($position, $plateNum, -1)) {
                    if ($this->isSolved($pos)) {
                        $this->solution = $pos;
                        return;
                    }
                    $key = $this->serializeState($pos['position']);
                    if (!isset($visited[$key])) {
                        $visited[$key] = true;
                        $queue[] = $pos;
                    }
                }
            }



            if ($this->limit > 1000000) {
                $this->error = 'limit over: ' . $this->limit;
                return;
            }

        }

    }


    public function movePlate($position, $plateNum, $direction)
    {
        $this->limit++;
        $pos = $position;

        $pos['history'][] = [$plateNum, $direction];

        if (!empty($pos['history'])) {
            if (count($pos['history']) >= 2) {
                if ($pos['history'][(count($pos['history']) - 1)][0] !== $pos['history'][(count($pos['history']) - 2)][0]) {
                    $pos['steps']++;
                }
            } else {
                $pos['steps']++;
            }
        }


        foreach ($position['position'] as $x => $y) {
            if ($x === $plateNum) {
                $pos['position'][$x] = $y + $direction;
            } else {
                $pos['position'][$x] = $y + $this->rulesMatrix[$plateNum][$x] * $direction;
            }
            if ($pos['position'][$x] < 0 || $pos['position'][$x] > 6) {
                return false;
            }
        }

        return $pos;
    }

    private function isSolved($position): bool
    {
        foreach ($position['position'] as $val) {
            if ($val !== 3) {
                return false;
            }
        }
        return true;
    }
}