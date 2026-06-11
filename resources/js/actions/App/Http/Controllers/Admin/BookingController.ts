import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/bookings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::index
* @see app/Http/Controllers/Admin/BookingController.php:14
* @route '/admin/bookings'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Admin\BookingController::generateMeet
* @see app/Http/Controllers/Admin/BookingController.php:25
* @route '/admin/bookings/{booking}/meeting'
*/
export const generateMeet = (args: { booking: number | { id: number } } | [booking: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateMeet.url(args, options),
    method: 'post',
})

generateMeet.definition = {
    methods: ["post"],
    url: '/admin/bookings/{booking}/meeting',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BookingController::generateMeet
* @see app/Http/Controllers/Admin/BookingController.php:25
* @route '/admin/bookings/{booking}/meeting'
*/
generateMeet.url = (args: { booking: number | { id: number } } | [booking: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { booking: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { booking: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            booking: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        booking: typeof args.booking === 'object'
        ? args.booking.id
        : args.booking,
    }

    return generateMeet.definition.url
            .replace('{booking}', parsedArgs.booking.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BookingController::generateMeet
* @see app/Http/Controllers/Admin/BookingController.php:25
* @route '/admin/bookings/{booking}/meeting'
*/
generateMeet.post = (args: { booking: number | { id: number } } | [booking: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateMeet.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::generateMeet
* @see app/Http/Controllers/Admin/BookingController.php:25
* @route '/admin/bookings/{booking}/meeting'
*/
const generateMeetForm = (args: { booking: number | { id: number } } | [booking: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateMeet.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BookingController::generateMeet
* @see app/Http/Controllers/Admin/BookingController.php:25
* @route '/admin/bookings/{booking}/meeting'
*/
generateMeetForm.post = (args: { booking: number | { id: number } } | [booking: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateMeet.url(args, options),
    method: 'post',
})

generateMeet.form = generateMeetForm

const BookingController = { index, generateMeet }

export default BookingController