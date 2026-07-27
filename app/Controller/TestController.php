<?php


namespace app\Controller;


use app\Service\GothicCodeResolver;
use app\Service\LockSolver;
use app\Service\Utils;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\TextResponse;
use Lynxx\AbstractController;
use Psr\Http\Message\ServerRequestInterface;

class TestController extends AbstractController
{

    private $request;
    private $gothic;

    /**
     * TestController constructor.
     */
    public function __construct(ServerRequestInterface $request, GothicCodeResolver $gothic)
    {
        $this->request = $request;
        $this->gothic = $gothic;
    }

    public function test()
    {
        $form = "
            <form method='post'>
                <input type='text' name='testFormNameInput' value='' />
                <input type='submit' value='ok'>
            </form>
        ";
        return new HtmlResponse(
            $form
            . '<br />hello from test controller with request:<br/> <pre>'
            . print_r($this->request->getParsedBody(), true)
            . '</pre>'
            . '<p>' . print_r($this->request->getQueryParams(), true) . '</p>'
        );
    }


    public function testGothic()
    {

        $position = [3, 1, 7, 6, 2];


        $rules = [
            [1, -1, -1, -1, -1],
            [-1, 1, 0, 0, 0],
            [-1, 0, 1, 0, 0],
            [0, 0, 1, 1, 0],
            [1, 0, 0, 0, 1],
        ];

        /*
        $rules = [
            [1, -1, 0, 0, 0, 0],
            [0, 1, 0, 0, 0, 0],
            [0, 0, 1, -1, 0, 0],
            [0, 0, 1, 1, 0, 0],
            [0, 0, 0, 1, 1, 1],
            [0, -1, 0, 0, 0, 1],
        ];
        */
        $this->gothic->init($position, $rules);

        if ($this->gothic->resolve()) {
            $text = Utils::debugObj($this->gothic->getSolution());
        } else {
            $text = Utils::debugObj($this->gothic->getError());
        }

        return new TextResponse($text);

    }

    //public function testGothic()
    public function testGothic_Alisa()
    {
        // --- Пример использования ---

        /*
        Пример замка из Gothic Remake:
        - 4 пластины: индексы 0..3, диапазон [-3, +3]
        - При сдвиге пластины 0: пластина 0 двигается +1, пластина 1 двигается −1 (в противоположную сторону)
        - При сдвиге пластины 1: только пластина 1 +1
        - При сдвиге пластины 2: пластина 2 +1, пластина 3 −1
        - При сдвиге пластины 3: только пластина 3 +1

        Записываем связи как [индекс, множитель]:
        */
        $connections = [
            0 => [[0, 1], [1, -1]],
            1 => [[1, 1]],
            2 => [[2, 1], [3, -1]],
            3 => [[3, 1], [2, 1]],
            4 => [[4, 1], [3, 1], [5, 1]],
            5 => [[5, 1], [1, -1]],
        ];

        $solver = new LockSolver(6, $connections);

        // Стартовое состояние (пример)
        $startState = [-3, -1, 0, -3, 1, -1];

        $solution = $solver->solve($startState);

        if ($solution === null) {
            echo "Решения не найдено.\n";
        } else {
            echo "Решение найдено за " . count($solution) . " ходов:\n";
            foreach ($solution as $step => $move) {
                $direction = $move['delta'] > 0 ? 'вправо' : 'влево';
                echo ($step + 1) . ". Пластина #{$move['plate']} — $direction\n";
            }
        }
    }


}