<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace Outlook\Events;

use Outlook\Authorizer\Authenticator;
use Outlook\Authorizer\Contracts\SessionContract;
use Outlook\Authorizer\Token;

class EventAuthorizer
{
    /**
     * @var Authenticator
     */
    protected $authenticator;

    /**
     * @var SessionContract
     */
    protected $sessionManager;

    /**
     * EventAuthorizer constructor.
     * @param Authenticator $authenticator
     * @param SessionContract $sessionManager
     */
    public function __construct(Authenticator $authenticator, SessionContract $sessionManager)
    {
        $this->authenticator = $authenticator;
        $this->sessionManager = $sessionManager;
        $this->authenticator->setSessionManager($this->sessionManager);
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->authenticator->getLoginUrl([
            'https://outlook.office.com/calendars.readwrite',
        ]);
    }

    /**
     * @param string|null $code
     * @return bool|Token
     */
    public function isAuthenticated($code = null)
    {
        $token = $this->sessionManager->get();
        if ($token && !$token->isExpired()) {
            return $token;
        }
        // we clean up any existing expired token
        $this->sessionManager->remove();
        // if not in session we capture code parameter and send request to get token
        return $this->authenticator->getToken($code);
    }
}
