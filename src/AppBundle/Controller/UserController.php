<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroups;

class UserController extends Controller
{
    /**
     * @Route("/users", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        $users = $this->getDoctrine()
                      ->getRepository('AppBundle:User')
                      ->findAll();

        $users = $this->get('jms_serializer')->serialize($users, 'json');

        return new Response($users);
    }


    /**
     * @Route("/users/{id}", methods={"GET"})
     * @param User $id
     * @return Response
     */
    public function show(User $id)
    {
        $user = $this->get('jms_serializer')->serialize($id, 'json');

        return new Response($user);
    }


    /**
     * @Route("/users", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $name = $request->get('name');

        $user = new User();
        $user->setName($name);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse('ok');
    }


    /**
     * @Route("/users/{id}", methods={"DELETE"})
     * @param User $id
     * @return JsonResponse
     */
    public function destroy(User $id)
    {
        $this->removeUserFromGroup($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($id);
        $entityManager->flush();

        return new JsonResponse('Removed');
    }


    /**
     * @Route("/users/groups", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function assignUserToGroup(Request $request)
    {
        $user_id = $request->get('user_id');
        $group_id = $request->get('group_id');

        if ($this->exists('User', $user_id)&& $this->exists('Groups', $group_id)) {

            if ( !$this->assigned($user_id, $group_id)) {
                $user_groups = new UserGroups();
                $user_groups->setUserId($user_id);
                $user_groups->setGroupId($group_id);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user_groups);
                $entityManager->flush();

                return new JsonResponse('Assigned');

            } else {
                return new JsonResponse('This User already is assign to this Group');
            }

        } else {
            return new JsonResponse('User or Group invalid');
        }
    }


    /**
     * @Route("/users/{user_id}/groups/{group_id}", methods={"DELETE"})
     * @param $user_id
     * @param $group_id
     * @return JsonResponse
     */
    public function removeUserFromGroup($user_id, $group_id = null)
    {
        $find = ['userId' => $user_id];
        if ($group_id) {
            $find = array_merge($find, ['groupId' => $group_id]);
        }

        $user_groups = $this->getDoctrine()
            ->getRepository('AppBundle:UserGroups')
            ->findBy($find);

        if ($user_groups) {

            $entityManager = $this->getDoctrine()->getManager();
            foreach ($user_groups as $user_group) {
                $entityManager->remove($user_group);
                $entityManager->flush();
            }
            return new JsonResponse('Removed');

        } else {
            return new JsonResponse('Not Found');
        }

    }


    /**
     * Check if User/Group exists
     *
     * @param $class
     * @param $id
     * @return bool
     */
    public function exists($class, $id)
    {
        $check = $this->getDoctrine()
                      ->getRepository('AppBundle:'.$class)
                      ->find($id);

        return $check ? true : false;
    }


    /**
     * Check if User is assigned to this Group
     *
     * @param $user
     * @param $group
     * @return bool
     */
    public function assigned($user, $group)
    {
        $check = $this->getDoctrine()
                      ->getRepository('AppBundle:UserGroups')
                      ->findBy([
                          'userId' => $user,
                          'groupId' => $group
                      ]);

        return $check ? true : false;
    }

}
