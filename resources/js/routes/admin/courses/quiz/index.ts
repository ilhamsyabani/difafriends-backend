import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
export const grade = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: grade.url(args, options),
    method: 'get',
})

grade.definition = {
    methods: ["get","head"],
    url: '/admin/courses/{course}/quizzes/{quiz}/grade',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
grade.url = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return grade.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace('{quiz}', parsedArgs.quiz.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
grade.get = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: grade.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
grade.head = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: grade.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
const gradeForm = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: grade.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
gradeForm.get = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: grade.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\QuizGradeController::grade
* @see app/Http/Controllers/Admin/QuizGradeController.php:17
* @route '/admin/courses/{course}/quizzes/{quiz}/grade'
*/
gradeForm.head = (args: { course: number | { id: number }, quiz: number | { id: number } } | [course: number | { id: number }, quiz: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: grade.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

grade.form = gradeForm

const quiz = {
    grade: Object.assign(grade, grade),
}

export default quiz