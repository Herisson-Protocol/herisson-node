<?php

namespace Herisson\Controller\Admin;

use Herisson\Controller\HerissonController;
use Herisson\Form\FriendType;
use Herisson\Service\Encryption\Encryptor;
use Herisson\Service\Network\AbstractGrabber;
use Herisson\Service\Protocol\FriendProtocol;
use Herisson\Service\Message;
use Herisson\Repository\FriendRepository;
use Herisson\Entity\Friend;
use Herisson\UseCase\Friend\AskForFriendRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends HerissonController
{

    /**
     * @var FriendRepository
     */
    private $repository;

    public function __construct(FriendRepository $repository)
    {
        $this->repository = $repository;
        //$this->loadEntityManager();
    }

    /**
     * Action to add a new friend
     *
     * @Route("/admin/friend/ask/{id}", name="admin.friend.ask")
     *
     * @param Friend $friend
     * @param FriendProtocol $protocol
     * @return void
     * @throws \Herisson\Service\Encryption\Exception
     * @throws \Herisson\Service\Network\Exception
     * @throws \Herisson\Service\Protocol\Exception
     */
    function askToFriendAction(Friend $friend, FriendProtocol $protocol) : Response
    {
        //$protocol = new FriendProtocol();
        $protocol->askForFriend($friend);
        return $this->redirectToRoute('admin.friend.index');
    }


    /**
     * Action to add a new friend
     *
     * @Route("/ask", name="admin.friend.friendrequest", methods={"POST","GET"})
     *
     * @param Friend $friend
     * @param FriendProtocol $protocol
     * @return void
     * @throws \Herisson\Service\Encryption\Exception
     * @throws \Herisson\Service\Network\Exception
     * @throws \Herisson\Service\Protocol\Exception
     */
    function friendAskingAction(Request $request, FriendProtocol $protocol) : Response
    {
        /*
        $arequest = AskForFriendRequest();
        $arequest->url = $request->request->get('url');
        $arequest->signature = $request->request->get('signature');
        $arequest->execute()
        */



        $friend = new Friend();
        /*
        $url = $request->request->get('url');
        $signature = $request->request->get('signature');
        $url = $request->query->get('url');
        $signature = $request->query->get('signature');
        */
        $url = "http://localhost:8001/";
        $signature = "Oo1Xzxjb0PO2YjXQPPJ+lL/KhUMo2BPTaEc0mORrXEwRpHjHszQtY6PRIoVw38eMbts0+ZYAyZJFEPZWtw2TNOiztj+fCKn/HLnCIFvLtJJc35fw40A32dCLrdirjQTROer8EcVl7jvU6++ojPvgPxkDYVRWZzDLCsdZVmV72JTwIU1Qi+C7XrrcljEnzZb9i6Ti7C8+zI10lgJAgbO8F9L6ymWyMavDxS7+2MC2tiSlp1mQ1G6PNHi/m/xoywkRHo7+lybS4cMo4RRQc9mJr+7t3Fij9xcX52Rh/FOk8iqvyhMKQGEhNCjNISRBfQXbfdfw3jlRmcF1omCH7dYKTA==";

        $friend->setUrl($url);
        $protocol->getInfo($friend);
        $protocol->reloadPublicKey($friend);
        dump($friend);
        $encryptor = new Encryptor();
        $uncipheredData = $encryptor->publicDecrypt($signature, $friend->getPublicKey());
        error_log("friendAskingAction");
        if ($uncipheredData == $url) {
            error_log("err code 200");
            AbstractGrabber::reply(200);
            exit;
        }
        AbstractGrabber::reply(500);
        error_log("err code 500");
        exit;


        //$protocol->ask($site, $friend);
    }



    /**
     * Action to approve a new friend
     *
     * Redirect to editAction()
     * @Route("/admin/friend/approve/{id}", name="admin.friend.approve")
     * @param Friend $friend
     * @param Message $message
     * @return Response
     */
    function approveAction(Friend $friend, Message $message) : Response
    {
            if ($friend->validateFriend()) {
                $message->addSucces("Friend has been notified of your approvement");
            } else {
                $message->addError("Something went wrong while adding friendFriend has been notified of your approvement");
            }
        // Redirect to Friends list
        return $this->redirectToRoute("admin.friend.index");

    }

    /**
     * Action to delete a friend
     *
     * Redirect to indexAction()
     * @Route("/admin/friend/delete/{id}", name="admin.friend.delete")
     * @param Friend $friend
     * @return Response
     */
    function deleteAction(Friend $friend) : Response
    {

        $this->repository->remove($friend);


        // TODO delete related backups and localbackups

        // Redirect to Friends list
        return $this->redirectToRoute('admin.friend.index');
    }

    /**
     * Action to edit a friend
     *
     * If POST method used, update the given friend with the POST parameters,
     * otherwise just display the friend properties
     *
     * @return void
     */
    /*
    function editActionOld()
    {
        $id = intval(param('id'));
        if (!$id) {
            $id = 0;
        }
        if (sizeof($_POST)) {
            $url = post('url');
            $alias = post('alias');

            $new = $id == 0 ? true : false;
            if ($new) {
                $friend = new Friend();
                $friend->is_active = 0;
            } else {
                $friend = FriendRepository::get($id);
            }

            $friend->alias = $alias;
            $friend->url = $url;
            if ($new) {
                $friend->getInfo();
                $friend->askForFriend();
            }
            $friend->save();
            if ($new) { 
                if ($new && $friend->is_active) {
                    Message::i()->addSucces("Friend has been added and automatically validated");
                } else {
                    Message::i()->addSucces("Friend has been added, but needs to be validated by its owner");
                }
                // Return to list after creating new friend.
                $this->indexAction();
                $this->setView('index');
                return;
            } else {
                Message::i()->addSucces("Friend saved");
            }
        }

        if ($id == 0) {
            $this->view->existing = new Friend();
        } else {
            $this->view->existing = FriendRepository::get($id);
        }
        $this->view->id = $id;
    }
    */

    /**
     * @Route("/admin/friend/edit/{id}", name="admin.friend.edit", requirements={"id":"\d+"})
     * @param Friend $friend
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editAction(Friend $friend, Request $request) : Response
    {
        $form = $this->createForm(FriendType::class, $friend);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Friend has been successfully modified');
            return $this->redirectToRoute('admin.friend.index');
        }

        return $this->render('admin/friend/edit.html.twig', [
            'friend' => $friend,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/admin/friend/new", name="admin.friend.new")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function newAction(Request $request) : Response
    {
        $friend = new Friend();
        $form = $this->createForm(FriendType::class, $friend);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($friend);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Friend has been successfully created');
            return $this->redirectToRoute('admin.friend.index');
        }

        return $this->render('admin/friend/new.html.twig', [
            'friend' => $friend,
            'form' => $form->createView(),
        ]);

    }


    /**
     * Action to list friends
     *
     * This is the default action
     *
     * @Route("/admin/friend/index", name="admin.friend.index")
     *
     * @return void
     */
    public function indexAction() : Response
    {
        $actives  = $this->repository->getAll();
        /*
        $youwant  = $this->repository->getWhere("is_youwant=1");
        $wantsyou = $this->repository->getWhere("is_wantsyou=1");
        $errors   = $this->repository->getWhere("is_wantsyou!=1 and is_youwant!=1 and is_active!=1");
        */

        return $this->render('admin/friend/index.html.twig', [
                'actives' => $actives,
                'friends' => $actives,
                /*
                'youwant' => $youwant,
                'wantsyou' => $wantsyou,
                'errors' => $errors,
                */
            ]
        );
    }

    /**
     * Action to import friends
     *
     * Not implemented yet
     *
     * @return void
     */
    /*
    function importAction()
    {
        if ( !empty($_POST['login']) && !empty($_POST['password'])) {
        }
    }
    */


}


