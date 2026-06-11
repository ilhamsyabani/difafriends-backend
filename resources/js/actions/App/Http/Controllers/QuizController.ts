import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
export const show = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/learn/{course}/quiz/{quiz}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
show.url = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            quiz: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return show.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
show.get = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
show.head = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
const showForm = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
showForm.get = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::show
* @see app/Http/Controllers/QuizController.php:17
* @route '/learn/{course}/quiz/{quiz}'
*/
showForm.head = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\QuizController::start
* @see app/Http/Controllers/QuizController.php:46
* @route '/learn/{course}/quiz/{quiz}/start'
*/
export const start = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

start.definition = {
    methods: ["post"],
    url: '/learn/{course}/quiz/{quiz}/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\QuizController::start
* @see app/Http/Controllers/QuizController.php:46
* @route '/learn/{course}/quiz/{quiz}/start'
*/
start.url = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            quiz: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return start.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::start
* @see app/Http/Controllers/QuizController.php:46
* @route '/learn/{course}/quiz/{quiz}/start'
*/
start.post = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QuizController::start
* @see app/Http/Controllers/QuizController.php:46
* @route '/learn/{course}/quiz/{quiz}/start'
*/
const startForm = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QuizController::start
* @see app/Http/Controllers/QuizController.php:46
* @route '/learn/{course}/quiz/{quiz}/start'
*/
startForm.post = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(args, options),
    method: 'post',
})

start.form = startForm

/**
* @see \App\Http\Controllers\QuizController::submit
* @see app/Http/Controllers/QuizController.php:69
* @route '/learn/{course}/quiz/{quiz}/submit'
*/
export const submit = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '/learn/{course}/quiz/{quiz}/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\QuizController::submit
* @see app/Http/Controllers/QuizController.php:69
* @route '/learn/{course}/quiz/{quiz}/submit'
*/
submit.url = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            quiz: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return submit.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::submit
* @see app/Http/Controllers/QuizController.php:69
* @route '/learn/{course}/quiz/{quiz}/submit'
*/
submit.post = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QuizController::submit
* @see app/Http/Controllers/QuizController.php:69
* @route '/learn/{course}/quiz/{quiz}/submit'
*/
const submitForm = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\QuizController::submit
* @see app/Http/Controllers/QuizController.php:69
* @route '/learn/{course}/quiz/{quiz}/submit'
*/
submitForm.post = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: submit.url(args, options),
    method: 'post',
})

submit.form = submitForm

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
export const result = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: result.url(args, options),
    method: 'get',
})

result.definition = {
    methods: ["get","head"],
    url: '/learn/{course}/quiz/{quiz}/result',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
result.url = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            quiz: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.slug
        : args.course,
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return result.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
result.get = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: result.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
result.head = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: result.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
const resultForm = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: result.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
resultForm.get = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: result.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\QuizController::result
* @see app/Http/Controllers/QuizController.php:152
* @route '/learn/{course}/quiz/{quiz}/result'
*/
resultForm.head = (args: { course: string | { slug: string }, quiz: number | { id: number } } | [course: string | { slug: string }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: result.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

result.form = resultForm

const QuizController = { show, start, submit, result }

export default QuizController