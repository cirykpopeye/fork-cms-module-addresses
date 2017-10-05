<?php
namespace Frontend\Modules\Addresses\Ajax;
use Backend\Modules\Addresses\Domain\Address\Address;
use Frontend\Core\Engine\Base\AjaxAction as FrontendBaseAJAXAction;
use Frontend\Core\Language\Locale;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: cirykpopeye
 * Date: 28/04/17
 * Time: 14:52
 */
class AddressInformation extends FrontendBaseAJAXAction
{
    public function execute(): void
    {

        /** @var Request $request */
        $request = $this->getRequest();

        $addressId = $request->request->getInt('addressId');

        $address = $this->get('addresses.repository.address')->find($addressId);
        if(!$address instanceof Address) {
            $this->output(Response::HTTP_NOT_FOUND, null, 'Do not add this address');
            return;
        }

        //-- Generate a template for this
        /** @var \Twig_Environment $twig */
        $twig = $this->get('twig');
        $template = $twig->render(FRONTEND_PATH . '/Modules/Addresses/Layout/Partials/Address.html.twig', array('address' => $address, 'trans' => $address->getTranslation(Locale::frontendLanguage())));
        $templateSmall = $twig->render(FRONTEND_PATH . '/Modules/Addresses/Layout/Partials/AddressSmall.html.twig', array('address' => $address, 'trans' => $address->getTranslation(Locale::frontendLanguage())));
        $this->output(Response::HTTP_OK, array('template' => $template, 'template_small' => $templateSmall, 'addressId' => $addressId, 'counter' => $request->request->get('counter')));
    }
}
