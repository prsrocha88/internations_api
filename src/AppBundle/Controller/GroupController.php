<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Groups;

class GroupController extends Controller
{
    /**
     * @Route("/groups", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        $groups = $this->getDoctrine()
            ->getRepository('AppBundle:Groups')
            ->findAll();

        $groups = $this->get('jms_serializer')->serialize($groups, 'json');

        return new Response($groups);
    }

    /**
     * @Route("/groups/{id}", methods={"GET"})
     * @param Groups $id
     * @return Response
     */
    public function show(Groups $id)
    {
        $groups = $this->get('jms_serializer')->serialize($id, 'json');

        return new Response($groups);
    }


    /**
     * @Route("/groups", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $name = $request->get('name');

        $groups = new Groups();
        $groups->setName($name);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($groups);
        $entityManager->flush();

        return new JsonResponse('ok');
    }


    /**
     * @Route("/groups/{id}", methods={"DELETE"})
     * @param Groups $id
     *
     * @return JsonResponse
     */
    public function destroy(Groups $id)
    {
        $user_groups = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroups')
            ->findBy(['groupId' => $id]);

        if (!$user_groups) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($id);
            $entityManager->flush();

            return new JsonResponse('Removed');

        } else {
            return new JsonResponse('Can\'t remove this group because have users');
        }


    }
}
