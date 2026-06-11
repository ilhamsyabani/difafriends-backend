import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ScalevWebhookController::handle
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
export const handle = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

handle.definition = {
    methods: ["post"],
    url: '/webhook/scalev',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ScalevWebhookController::handle
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
handle.url = (options?: RouteQueryOptions) => {
    return handle.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScalevWebhookController::handle
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
handle.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScalevWebhookController::handle
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
const handleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handle.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScalevWebhookController::handle
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
handleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: handle.url(options),
    method: 'post',
})

handle.form = handleForm

const ScalevWebhookController = { handle }

export default ScalevWebhookController