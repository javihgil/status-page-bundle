# Status Page Bundle

*This bundle is under development, not yet stable*
 
Status Page Bundle allows to store some metrics in redis server to provide a status page.

## Configuration

    # app/config/config.yml
    jhg_status_page:
        predis_client_id: predis_service_id
        metrics:
            requests_per_minute:
                type: request_count
                period: minute
                expire: "+24 hours"
                condition: "not (request.getPathInfo() matches '/^\\\\/_status/i')"
            response_count_404:
                type: response_count
                period: minute
                expire: "next day"
                condition: "response.getStatusCode() == 404"
            response_time:
                type: response_time
                period: minute
                expire: "+24 hours"
                condition: "not (request.getPathInfo() matches '/^\\\\/_status/i')"
            exception:
                type: exception
                period: minute
                expire: "tomorrow"
            requests_per_hour:
                type: request_count
                period: hour
                expire: "next month"
            api_requests_per_minute:
                type: request_count
                period: minute
                expire: "+24 hours"
                condition: "request.getPathInfo() matches '/^\\\\/api/i'"
    
    # routing.yml
    _status_page:
        resource: "@JhgStatusPageBundle/Resources/config/routing.yml"
        prefix: /status

## Custom status events

For example, if you want to store each FOSUserBundle success register, you can implement this status listener:
 
**AppBundle/EventListener/LoginStatusListener.php**

    <?php
    
    namespace AppBundle\EventListener;
    
    use FOS\UserBundle\Event\FormEvent;
    use FOS\UserBundle\FOSUserEvents;
    use Jhg\StatusPageBundle\Status\StatusCount;
    use Jhg\StatusPageBundle\StatusListener\AbstractStatusListener;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    
    class RegistrationStatusListener extends AbstractStatusListener implements EventSubscriberInterface
    {
        public static function getSubscribedEvents()
        {
            return [
                FOSUserEvents::REGISTRATION_SUCCESS => ['onUserRegister' => 1024],
            ];
        }
    
        public function onUserLogin(FormEvent $event)
        {
            $this->statusStack->registerStatus(new StatusCount($this->eventKey, $this->eventPeriod, $this->eventExpire));
        }
    }
 
**app/config/config.yml**

    jhg_status_page:
        predis_client_id: predis_service_id
        metrics:
            requests_per_minute:
                type: custom
                class: AppBundle\StatusListener\RegistrationStatusListener
                period: minute
                expire: "+24 hours"
                
                