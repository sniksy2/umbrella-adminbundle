<?php

namespace Umbrella\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Symfony\Component\Translation\t;

use Umbrella\AdminBundle\Entity\BaseAdminUser;
use Umbrella\AdminBundle\Service\UserManagerInterface;
use Umbrella\AdminBundle\UmbrellaAdminConfiguration;
use Umbrella\CoreBundle\Controller\BaseController;

class ProfileController extends BaseController
{
    public const PROFILE_ROUTE = 'umbrella_admin_profile_index';

    /**
     * ProfileController constructor.
     */
    public function __construct(protected UserManagerInterface $userManager, protected UmbrellaAdminConfiguration $config)
    {
    }

    public function index(Request $request)
    {
        $user = $this->getUser();

        if (!$user instanceof BaseAdminUser) {
            throw new NotFoundHttpException(sprintf('Profile view are only available for fully authenticate %s user.', BaseAdminUser::class));
        }

        $settingsForm = $this->createForm($this->config->userProfileForm(), $user);
        $settingsForm->handleRequest($request);

        if ($settingsForm->isSubmitted() && $settingsForm->isValid()) {
            $this->userManager->update($user);

            $this->toastSuccess(t('alert.account_updated', [], 'UmbrellaAdmin'));

            return $this->redirectToRoute(self::PROFILE_ROUTE);
        }

        return $this->render('@UmbrellaAdmin/Profile/index.html.twig', [
            'user' => $user,
            'settings_form' => $settingsForm->createView(),
        ]);
    }
}
