import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import users from './users'
import categories from './categories'
import courses from './courses'
import schedules from './schedules'
import orders from './orders'
import companions from './companions'
import bookings from './bookings'
import activities from './activities'
import attendanceForms from './attendance-forms'
import attendanceSessions from './attendance-sessions'
import reports from './reports'
import activityLog from './activity-log'
import articles from './articles'
import gallery from './gallery'
import quiz from './quiz'
/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/admin/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/web.php:221
* @route '/admin/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

const admin = {
    dashboard: Object.assign(dashboard, dashboard),
    users: Object.assign(users, users),
    categories: Object.assign(categories, categories),
    courses: Object.assign(courses, courses),
    schedules: Object.assign(schedules, schedules),
    orders: Object.assign(orders, orders),
    companions: Object.assign(companions, companions),
    bookings: Object.assign(bookings, bookings),
    activities: Object.assign(activities, activities),
    attendanceForms: Object.assign(attendanceForms, attendanceForms),
    attendanceSessions: Object.assign(attendanceSessions, attendanceSessions),
    reports: Object.assign(reports, reports),
    activityLog: Object.assign(activityLog, activityLog),
    articles: Object.assign(articles, articles),
    gallery: Object.assign(gallery, gallery),
    quiz: Object.assign(quiz, quiz),
}

export default admin