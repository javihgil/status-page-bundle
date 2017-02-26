# Status Page Bundle

*This bundle is under development, not yet stable*
 
Status Page Bundle allows to store some metrics in redis server to provide a status page.

## Configuration

    # app/config/config.yml
    jhg_status_page:
        redis_client_id: snc_redis.status
    #    metrics:
    #        requests_per_minute:
    #            type: request_count
    #            period: minute
    #            expire: "+24 hours"
    #        response_time:
    #            type: response_time
    #            period: minute
    #            expire: "+24 hours"
    #        requests_per_hour:
    #            type: request_count
    #            period: hour
    #            expire: "+30 days"
    #        success_login:
    #            type: custom_count
    #            period: minute
    #            expire: "+24 hours"
    #        api_requests_per_minute:
    #            type: request_count
    #            period: minute
    #            expire: "+24 hours"
    #            condition: "request.path matches '/\/api/i'"

    
    # routing.yml
    _status_page:
        resource: "@JhgStatusPageBundle/Resources/config/routing.yml"
        prefix: /status
