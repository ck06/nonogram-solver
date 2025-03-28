<?php declare(strict_types=1);

namespace App\Controller;

use App\DTO\Board;
use App\Service\NonogramSolver;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NonogramController extends AbstractController
{
    public function __construct(private NonogramSolver $solver)
    {
    }

    // TODO: rewrite once we get actual data
    public function load(Request $request): Response
    {
        $data = $request->request->get('board');
        if (!$data) {
            return new Response('No board data found in request', Response::HTTP_BAD_REQUEST);
        }

        try {
            $solvedBoard = $this->solver->solve(new Board($data));
            return new JsonResponse($solvedBoard->toJson());
        } catch (Exception) {
            return new Response(
                'Something went wrong trying to solve this nonogram',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
