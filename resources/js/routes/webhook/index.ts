import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\OrderController::midtrans
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
export const midtrans = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: midtrans.url(options),
    method: 'post',
})

midtrans.definition = {
    methods: ["post"],
    url: '/webhook/midtrans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OrderController::midtrans
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
midtrans.url = (options?: RouteQueryOptions) => {
    return midtrans.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OrderController::midtrans
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
midtrans.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: midtrans.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::midtrans
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
const midtransForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: midtrans.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OrderController::midtrans
* @see app/Http/Controllers/OrderController.php:92
* @route '/webhook/midtrans'
*/
midtransForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: midtrans.url(options),
    method: 'post',
})

midtrans.form = midtransForm

/**
* @see \App\Http\Controllers\ScalevWebhookController::scalev
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
export const scalev = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scalev.url(options),
    method: 'post',
})

scalev.definition = {
    methods: ["post"],
    url: '/webhook/scalev',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ScalevWebhookController::scalev
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
scalev.url = (options?: RouteQueryOptions) => {
    return scalev.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ScalevWebhookController::scalev
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
scalev.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scalev.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScalevWebhookController::scalev
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
const scalevForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: scalev.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ScalevWebhookController::scalev
* @see app/Http/Controllers/ScalevWebhookController.php:18
* @route '/webhook/scalev'
*/
scalevForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: scalev.url(options),
    method: 'post',
})

scalev.form = scalevForm

const webhook = {
    midtrans: Object.assign(midtrans, midtrans),
    scalev: Object.assign(scalev, scalev),
}

export default webhook