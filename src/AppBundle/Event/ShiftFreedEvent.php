<?php

namespace AppBundle\Event;

use AppBundle\Entity\Shift;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class ShiftFreedEvent extends Event
{
    const NAME = 'shift.freed';

    private $shift;
    private $user;

    public function __construct(Shift $shift, User $user)
    {
        $this->shift = $shift;
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Shift
     */
    public function getShift()
    {
        return $this->shift;
    }

}
