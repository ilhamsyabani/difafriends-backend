import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
export const index = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/instructor/courses/{course}/quizzes/{quiz}/grade',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
index.url = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            course: args[0],
            quiz: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: typeof args.course === 'object'
        ? args.course.id
        : args.course,
        quiz: typeof args.quiz === 'object'
        ? args.quiz.id
        : args.quiz,
    }

    return index.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
index.get = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
index.head = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
const indexForm = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
indexForm.get = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::index
* @see app/Http/Controllers/Instructor/QuizGradeController.php:16
* @route '/instructor/courses/{course}/quizzes/{quiz}/grade'
*/
indexForm.head = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::grade
* @see app/Http/Controllers/Instructor/QuizGradeController.php:36
* @route '/instructor/quiz-answers/{answer}/grade'
*/
export const grade = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

grade.definition = {
    methods: ["post"],
    url: '/instructor/quiz-answers/{answer}/grade',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::grade
* @see app/Http/Controllers/Instructor/QuizGradeController.php:36
* @route '/instructor/quiz-answers/{answer}/grade'
*/
grade.url = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { answer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { answer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            answer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        answer: typeof args.answer === 'object'
        ? args.answer.id
        : args.answer,
    }

    return grade.definition.url
            .replace('{answer}', parsedArgs.answer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::grade
* @see app/Http/Controllers/Instructor/QuizGradeController.php:36
* @route '/instructor/quiz-answers/{answer}/grade'
*/
grade.post = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: grade.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::grade
* @see app/Http/Controllers/Instructor/QuizGradeController.php:36
* @route '/instructor/quiz-answers/{answer}/grade'
*/
const gradeForm = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: grade.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Instructor\QuizGradeController::grade
* @see app/Http/Controllers/Instructor/QuizGradeController.php:36
* @route '/instructor/quiz-answers/{answer}/grade'
*/
gradeForm.post = (args: { answer: number | { id: number } } | [answer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: grade.url(args, options),
    method: 'post',
})

grade.form = gradeForm

const QuizGradeController = { index, grade }

export default QuizGradeController