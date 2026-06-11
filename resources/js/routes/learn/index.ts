import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
export const show = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/learn/{course}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
show.url = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { course: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
    }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
show.get = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
show.head = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
const showForm = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
showForm.get = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LearnController::show
* @see app/Http/Controllers/LearnController.php:16
* @route '/learn/{course}'
*/
showForm.head = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\LearnController::progress
* @see app/Http/Controllers/LearnController.php:70
* @route '/learn/{course}/progress'
*/
export const progress = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: progress.url(args, options),
    method: 'post',
})

progress.definition = {
    methods: ["post"],
    url: '/learn/{course}/progress',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LearnController::progress
* @see app/Http/Controllers/LearnController.php:70
* @route '/learn/{course}/progress'
*/
progress.url = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { course: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
    }

    return progress.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LearnController::progress
* @see app/Http/Controllers/LearnController.php:70
* @route '/learn/{course}/progress'
*/
progress.post = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: progress.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LearnController::progress
* @see app/Http/Controllers/LearnController.php:70
* @route '/learn/{course}/progress'
*/
const progressForm = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: progress.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LearnController::progress
* @see app/Http/Controllers/LearnController.php:70
* @route '/learn/{course}/progress'
*/
progressForm.post = (args: { course: string | { slug: string } } | [course: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: progress.url(args, options),
    method: 'post',
})

progress.form = progressForm

const learn = {
    show: Object.assign(show, show),
    progress: Object.assign(progress, progress),
}

export default learn