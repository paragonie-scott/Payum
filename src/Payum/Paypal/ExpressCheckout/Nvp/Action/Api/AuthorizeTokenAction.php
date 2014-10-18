<?php
namespace Payum\Paypal\ExpressCheckout\Nvp\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\LogicException;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\AuthorizeToken;

class AuthorizeTokenAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     * 
     * @throws \Payum\Core\Exception\LogicException if the token not set in the instruction.
     * @throws \Payum\Core\Reply\HttpRedirect if authorization required.
     */
    public function execute($request)
    {
        /** @var $request \Payum\Paypal\ExpressCheckout\Nvp\Request\Api\AuthorizeToken */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }
        
        $model = ArrayObject::ensureArrayObject($request->getModel());
        if (false == $model['TOKEN']) {
            throw new LogicException('The TOKEN must be set by SetExpressCheckout request but it was not executed or failed. Review payment details model for more information');
        }
          
        if (false == $model['PAYERID'] || $request->isForced()) {
            throw new HttpRedirect(
                $this->api->getAuthorizeTokenUrl($model['TOKEN'], array(
                    'USERACTION' => $model['AUTHORIZE_TOKEN_USERACTION'],
                    'CMD' => $model['AUTHORIZE_TOKEN_CMD'],
                ))
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return 
            $request instanceof AuthorizeToken &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}