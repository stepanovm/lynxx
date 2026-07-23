<?php

namespace app\Service;

class LockSolver
{
    private int $plateCount;
    /**
     * connections[i] = [ [idx, factor], [idx, factor], ... ]
     * factor = +1 — двигается в ту же сторону, −1 — в противоположную
     */
    private array $connections;
    private int $minPos = -3;
    private int $maxPos = 3;

    /**
     * @param int $plateCount Количество пластин
     * @param array $connections connections[i] = [[idx, factor], ...]
     */
    public function __construct(int $plateCount, array $connections)
    {
        $this->plateCount = $plateCount;
        $this->connections = $connections;
    }

    /**
     * Применяет ход: сдвигает целевую пластину и все связанные с нужным множителем.
     * Возвращает новое состояние или null, если ход недопустим (штифт выходит за границы).
     */
    private function applyMove(array $state, int $plateIndex, int $delta): ?array
    {
        $newState = $state;

        foreach ($this->connections[$plateIndex] as [$idx, $factor]) {
            $shift = $delta * $factor;
            $newState[$idx] += $shift;

            // Если хоть один штифт выходит за границы — ход запрещён
            if ($newState[$idx] < $this->minPos || $newState[$idx] > $this->maxPos) {
                return null;
            }
        }

        return $newState;
    }

    private function serializeState(array $state): string
    {
        return implode(',', $state);
    }

    /**
     * BFS поиск кратчайшего пути к состоянию [0, 0, ..., 0].
     * Возвращает массив ходов: ['plate' => индекс, 'delta' => +1/-1]
     * Если решения нет — возвращает null.
     */
    public function solve(array $startState): ?array
    {
        if (count($startState) !== $this->plateCount) {
            throw new InvalidArgumentException('Неверное количество пластин в стартовом состоянии');
        }

        $goalState = array_fill(0, $this->plateCount, 0);
        $startKey = $this->serializeState($startState);
        $goalKey = $this->serializeState($goalState);

        if ($startKey === $goalKey) {
            return [];
        }

        // Очередь BFS: [state, path]
        $queue = [[$startState, []]];
        $visited = [$startKey => true];

        while (!empty($queue)) {
            [$currentState, $path] = array_shift($queue);

            for ($i = 0; $i < $this->plateCount; $i++) {
                foreach ([-1, 1] as $delta) {
                    $nextState = $this->applyMove($currentState, $i, $delta);
                    if ($nextState === null) {
                        continue; // недопустимый ход
                    }

                    $key = $this->serializeState($nextState);
                    if (isset($visited[$key])) {
                        continue;
                    }

                    $newPath = array_merge($path, [['plate' => $i, 'delta' => $delta]]);

                    if ($key === $goalKey) {
                        return $newPath;
                    }

                    $visited[$key] = true;
                    $queue[] = [$nextState, $newPath];
                }
            }
        }

        return null; // решение не найдено
    }
}