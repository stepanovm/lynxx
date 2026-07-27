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
                    'plate' => $currentPlate + 1,
                    'direction' => $history[1] === 1 ? 'Налево' : 'Направо',
                    'count' => 1,
                ];
            } else {
                $solution[$step]['count']++;
            }
        }
        foreach ($solution as &$item) {
            $item['text'] = 'Пластина ' . $item['plate'] . ' ' . mb_strtoupper($item['direction']) . ' x' . $item['count'];
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

        $solvesCount = 0;

        while(!empty($queue)) {

            $position = array_shift($queue);

            for ($plateNum = 0; $plateNum < $this->platesCount; $plateNum++) {
                foreach ([-1, 1] as $direction) {
                    if ($pos = $this->movePlate($position, $plateNum, $direction)) {
                        if ($this->isSolved($pos)) {
                            if ($solvesCount > 6) {
                                $this->solution = $pos;
                                return;
                            } else {
                                $solvesCount++;
                                $this->solution = $pos;
                                echo "<p>SOLUTION ".$solvesCount."</p>";
                                echo Utils::debugObj($this->getSolution());
                                continue;
                            }
                        }
                        $key = $this->serializeState($pos['position']);
                        if (!array_key_exists($key, $visited)) {
                            $visited[$key] = true;
                            $queue[] = $pos;
                        }
                    }
                }
            }

            if ($this->limit > 10000000) {
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
            $pos['position'][$x] = $y + $this->rulesMatrix[$plateNum][$x] * $direction;
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