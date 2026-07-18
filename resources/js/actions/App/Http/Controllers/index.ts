import CourseController from './CourseController'
import CompanionController from './CompanionController'
import ArticleController from './ArticleController'
import CertificateController from './CertificateController'
import PublicAttendanceController from './PublicAttendanceController'
import Auth from './Auth'
import OrderController from './OrderController'
import ScalevWebhookController from './ScalevWebhookController'
import LearnController from './LearnController'
import ReviewController from './ReviewController'
import QuizController from './QuizController'
import BookingController from './BookingController'
import UserOrderController from './UserOrderController'
import AssessmentController from './AssessmentController'
import Admin from './Admin'
import Instructor from './Instructor'
import Companion from './Companion'
import Settings from './Settings'

const Controllers = {
    CourseController: Object.assign(CourseController, CourseController),
    CompanionController: Object.assign(CompanionController, CompanionController),
    ArticleController: Object.assign(ArticleController, ArticleController),
    CertificateController: Object.assign(CertificateController, CertificateController),
    PublicAttendanceController: Object.assign(PublicAttendanceController, PublicAttendanceController),
    Auth: Object.assign(Auth, Auth),
    OrderController: Object.assign(OrderController, OrderController),
    ScalevWebhookController: Object.assign(ScalevWebhookController, ScalevWebhookController),
    LearnController: Object.assign(LearnController, LearnController),
    ReviewController: Object.assign(ReviewController, ReviewController),
    QuizController: Object.assign(QuizController, QuizController),
    BookingController: Object.assign(BookingController, BookingController),
    UserOrderController: Object.assign(UserOrderController, UserOrderController),
    AssessmentController: Object.assign(AssessmentController, AssessmentController),
    Admin: Object.assign(Admin, Admin),
    Instructor: Object.assign(Instructor, Instructor),
    Companion: Object.assign(Companion, Companion),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers