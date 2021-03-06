<?php
/**
 * Created by PhpStorm.
 * User: Ri
 * Date: 27.07.2016
 * Time: 0:13
 */

namespace AppBundle\Security\Core\User;


use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Facebook\FacebookRequest;
use Symfony\Component\HttpFoundation\Request;


class FOSUBUserProvider extends BaseFOSUBUserProvider
{

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        //on connect - get the access token and the user ID
        $service      = $response->getResourceOwner()->getName();
        $setter       = 'set'.ucfirst($service);
        $setter_id    = $setter.'Id';
        $setter_token = $setter.'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $username = $response->getUsername();
        $user     = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        $attr     = $response->getResponse();
        if (!empty($attr["friends"]["data"])) {
//            $request = new Request();
//            $request->getSession()->set('user_friends',$attr["friends"]["data"]);
            $_SESSION['user_friends'] = $attr["friends"]["data"];
        }


        //when the user is registrating
        if (null === $user) {
            $service      = $response->getResourceOwner()->getName();
            $setter       = 'set'.ucfirst($service);
            $setter_id    = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($attr["name"]);
            $user->setEmail($username);
            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            return $user;
        }
        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter      = 'set'.ucfirst($serviceName).'AccessToken';
        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }


}