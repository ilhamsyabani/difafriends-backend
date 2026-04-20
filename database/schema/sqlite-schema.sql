CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "photo" varchar,
  "phone" varchar,
  "birth_date" date,
  "gender" varchar check("gender" in('male', 'female', 'other')),
  "address" varchar,
  "city" varchar,
  "country" varchar not null default 'Indonesia',
  "role" varchar not null default 'user',
  "is_active" tinyint(1) not null default '1',
  "remember_token" varchar,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "two_factor_secret" text,
  "two_factor_recovery_codes" text,
  "two_factor_confirmed_at" datetime,
  "bio" text
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_reserved_at_available_at_index" on "jobs"(
  "queue",
  "reserved_at",
  "available_at"
);
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "parent_id" integer,
  "name" varchar not null,
  "slug" varchar not null,
  "image" varchar,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("parent_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "courses"(
  "id" integer primary key autoincrement not null,
  "category_id" integer not null,
  "instructor_id" integer not null,
  "title" varchar not null,
  "slug" varchar not null,
  "description" text,
  "thumbnail" varchar,
  "preview_video" varchar,
  "price" numeric not null default '0',
  "discount_price" numeric,
  "duration_minutes" integer not null default '0',
  "has_certificate" tinyint(1) not null default '0',
  "prerequisites" text,
  "is_featured" tinyint(1) not null default '0',
  "status" varchar not null default 'draft',
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "scalev_product_id" varchar,
  foreign key("category_id") references "categories"("id") on delete restrict,
  foreign key("instructor_id") references "users"("id") on delete restrict
);
CREATE UNIQUE INDEX "courses_slug_unique" on "courses"("slug");
CREATE TABLE IF NOT EXISTS "course_goals"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "goal_name" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("course_id") references "courses"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_sections"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "title" varchar not null,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("course_id") references "courses"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "certificates"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "course_id" integer not null,
  "certificate_number" varchar not null,
  "file_path" varchar,
  "issued_at" datetime not null,
  "confirmed_at" datetime,
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete restrict,
  foreign key("course_id") references "courses"("id") on delete restrict
);
CREATE UNIQUE INDEX "certificates_certificate_number_unique" on "certificates"(
  "certificate_number"
);
CREATE TABLE IF NOT EXISTS "course_lectures"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "section_id" integer not null,
  "title" varchar,
  "type" varchar not null default 'video',
  "url" varchar,
  "content" text,
  "video_duration" numeric,
  "is_free_preview" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("course_id") references "courses"("id") on delete cascade,
  foreign key("section_id") references "course_sections"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "course_resources"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "lecture_id" integer not null,
  "title" varchar not null,
  "type" varchar not null default 'link',
  "value" varchar not null,
  "file_size" varchar,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("course_id") references "courses"("id") on delete cascade,
  foreign key("lecture_id") references "course_lectures"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "orders"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "orderable_type" varchar not null,
  "orderable_id" integer not null,
  "item_name" varchar not null,
  "original_price" numeric not null,
  "discount_amount" numeric not null default '0',
  "final_amount" numeric not null,
  "status" varchar not null default 'pending',
  "expired_at" datetime,
  "invoice_number" varchar,
  "invoice_path" varchar,
  "invoice_generated_at" datetime,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete restrict
);
CREATE INDEX "orders_orderable_type_orderable_id_index" on "orders"(
  "orderable_type",
  "orderable_id"
);
CREATE INDEX "orders_status_index" on "orders"("status");
CREATE UNIQUE INDEX "orders_invoice_number_unique" on "orders"(
  "invoice_number"
);
CREATE TABLE IF NOT EXISTS "tutor_schedules"(
  "id" integer primary key autoincrement not null,
  "tutor_id" integer not null,
  "day_of_week" integer not null default '0',
  "start_time" time not null,
  "end_time" time not null,
  "session_duration" integer not null default '60',
  "break_time" integer not null default '15',
  "price" numeric not null,
  "max_participants" integer not null default '1',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("tutor_id") references "users"("id") on delete restrict
);
CREATE UNIQUE INDEX "tutor_schedules_tutor_id_day_of_week_start_time_unique" on "tutor_schedules"(
  "tutor_id",
  "day_of_week",
  "start_time"
);
CREATE TABLE IF NOT EXISTS "bookings"(
  "id" integer primary key autoincrement not null,
  "student_id" integer not null,
  "tutor_id" integer not null,
  "tutor_schedule_id" integer not null,
  "start_at" datetime not null,
  "end_at" datetime not null,
  "type" varchar not null default 'online',
  "status" varchar not null default 'pending',
  "meeting_link" varchar,
  "notes" text,
  "session_notes" text,
  "confirmed_at" datetime,
  "completed_at" datetime,
  "cancelled_at" datetime,
  "cancellation_reason" text,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("student_id") references "users"("id") on delete restrict,
  foreign key("tutor_id") references "users"("id") on delete restrict,
  foreign key("tutor_schedule_id") references "tutor_schedules"("id") on delete restrict
);
CREATE UNIQUE INDEX "bookings_tutor_id_start_at_unique" on "bookings"(
  "tutor_id",
  "start_at"
);
CREATE TABLE IF NOT EXISTS "payments"(
  "id" integer primary key autoincrement not null,
  "order_id" integer not null,
  "midtrans_transaction_id" varchar not null,
  "snap_token" varchar,
  "payment_type" varchar,
  "payment_bank" varchar,
  "amount" numeric not null,
  "status" varchar not null default 'pending',
  "midtrans_response" text,
  "paid_at" datetime,
  "expired_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references "orders"("id") on delete restrict
);
CREATE INDEX "payments_order_id_index" on "payments"("order_id");
CREATE INDEX "payments_status_index" on "payments"("status");
CREATE INDEX "payments_midtrans_transaction_id_index" on "payments"(
  "midtrans_transaction_id"
);
CREATE UNIQUE INDEX "payments_midtrans_transaction_id_unique" on "payments"(
  "midtrans_transaction_id"
);
CREATE TABLE IF NOT EXISTS "course_progress"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "course_id" integer not null,
  "lecture_id" integer not null,
  "is_completed" tinyint(1) not null default '0',
  "watch_seconds" integer not null default '0',
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete restrict,
  foreign key("course_id") references "courses"("id") on delete restrict,
  foreign key("lecture_id") references "course_lectures"("id") on delete cascade
);
CREATE UNIQUE INDEX "course_progress_user_id_lecture_id_unique" on "course_progress"(
  "user_id",
  "lecture_id"
);
CREATE INDEX "course_progress_user_id_course_id_index" on "course_progress"(
  "user_id",
  "course_id"
);
CREATE TABLE IF NOT EXISTS "reviews"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "reviewable_type" varchar not null,
  "reviewable_id" integer not null,
  "rating" integer not null,
  "comment" text,
  "reply" text,
  "is_published" tinyint(1) not null default '1',
  "published_at" datetime,
  "deleted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete restrict
);
CREATE INDEX "reviews_reviewable_type_reviewable_id_index" on "reviews"(
  "reviewable_type",
  "reviewable_id"
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" integer not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE TABLE IF NOT EXISTS "quizzes"(
  "id" integer primary key autoincrement not null,
  "course_id" integer not null,
  "section_id" integer,
  "title" varchar not null,
  "description" text,
  "is_required" tinyint(1) not null default '0',
  "passing_score" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "type" varchar not null default 'quiz',
  foreign key("course_id") references "courses"("id") on delete cascade,
  foreign key("section_id") references "course_sections"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "quiz_questions"(
  "id" integer primary key autoincrement not null,
  "quiz_id" integer not null,
  "type" varchar not null default 'multiple_choice',
  "question" text not null,
  "points" integer not null,
  "sort_order" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("quiz_id") references "quizzes"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_options"(
  "id" integer primary key autoincrement not null,
  "question_id" integer not null,
  "option_text" varchar not null,
  "is_correct" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("question_id") references "quiz_questions"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_attempts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "quiz_id" integer not null,
  "score" integer,
  "status" varchar not null default 'pending',
  "started_at" datetime not null default CURRENT_TIMESTAMP,
  "submitted_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("quiz_id") references "quizzes"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_answers"(
  "id" integer primary key autoincrement not null,
  "attempt_id" integer not null,
  "question_id" integer not null,
  "selected_option_id" integer,
  "essay_answer" text,
  "points_earned" integer,
  "instructor_note" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("attempt_id") references "quiz_attempts"("id") on delete cascade,
  foreign key("question_id") references "quiz_questions"("id") on delete cascade,
  foreign key("selected_option_id") references "quiz_options"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "articles"(
  "id" integer primary key autoincrement not null,
  "author_id" integer not null,
  "title" varchar not null,
  "slug" varchar not null,
  "thumbnail" varchar,
  "content" text not null,
  "status" varchar not null default 'draft',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("author_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "articles_slug_unique" on "articles"("slug");
CREATE TABLE IF NOT EXISTS "site_settings"(
  "key" varchar not null,
  "value" text,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "enrollments"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "course_id" integer not null,
  "order_id" integer,
  "status" varchar not null default('active'),
  "enrolled_at" datetime not null,
  "expired_at" datetime,
  "completed_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("order_id") references orders("id") on delete restrict on update no action,
  foreign key("course_id") references courses("id") on delete restrict on update no action,
  foreign key("user_id") references users("id") on delete restrict on update no action
);
CREATE INDEX "enrollments_status_index" on "enrollments"("status");
CREATE UNIQUE INDEX "enrollments_user_id_course_id_unique" on "enrollments"(
  "user_id",
  "course_id"
);
CREATE UNIQUE INDEX "courses_scalev_product_id_unique" on "courses"(
  "scalev_product_id"
);
CREATE TABLE IF NOT EXISTS "pulse_values"(
  "id" integer primary key autoincrement not null,
  "timestamp" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "value" text not null
);
CREATE INDEX "pulse_values_timestamp_index" on "pulse_values"("timestamp");
CREATE INDEX "pulse_values_type_index" on "pulse_values"("type");
CREATE UNIQUE INDEX "pulse_values_type_key_hash_unique" on "pulse_values"(
  "type",
  "key_hash"
);
CREATE TABLE IF NOT EXISTS "pulse_entries"(
  "id" integer primary key autoincrement not null,
  "timestamp" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "value" integer
);
CREATE INDEX "pulse_entries_timestamp_index" on "pulse_entries"("timestamp");
CREATE INDEX "pulse_entries_type_index" on "pulse_entries"("type");
CREATE INDEX "pulse_entries_key_hash_index" on "pulse_entries"("key_hash");
CREATE INDEX "pulse_entries_timestamp_type_key_hash_value_index" on "pulse_entries"(
  "timestamp",
  "type",
  "key_hash",
  "value"
);
CREATE TABLE IF NOT EXISTS "pulse_aggregates"(
  "id" integer primary key autoincrement not null,
  "bucket" integer not null,
  "period" integer not null,
  "type" varchar not null,
  "key" text not null,
  "key_hash" varchar not null,
  "aggregate" varchar not null,
  "value" numeric not null,
  "count" integer
);
CREATE UNIQUE INDEX "pulse_aggregates_bucket_period_type_aggregate_key_hash_unique" on "pulse_aggregates"(
  "bucket",
  "period",
  "type",
  "aggregate",
  "key_hash"
);
CREATE INDEX "pulse_aggregates_period_bucket_index" on "pulse_aggregates"(
  "period",
  "bucket"
);
CREATE INDEX "pulse_aggregates_type_index" on "pulse_aggregates"("type");
CREATE INDEX "pulse_aggregates_period_type_aggregate_bucket_index" on "pulse_aggregates"(
  "period",
  "type",
  "aggregate",
  "bucket"
);
CREATE TABLE IF NOT EXISTS "activity_log"(
  "id" integer primary key autoincrement not null,
  "log_name" varchar,
  "description" text not null,
  "subject_type" varchar,
  "subject_id" integer,
  "event" varchar,
  "causer_type" varchar,
  "causer_id" integer,
  "attribute_changes" text,
  "properties" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "subject" on "activity_log"("subject_type", "subject_id");
CREATE INDEX "causer" on "activity_log"("causer_type", "causer_id");
CREATE INDEX "activity_log_log_name_index" on "activity_log"("log_name");

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_08_14_170933_add_two_factor_columns_to_users_table',1);
INSERT INTO migrations VALUES(5,'2026_03_15_154040_create_categories_table',1);
INSERT INTO migrations VALUES(6,'2026_03_15_154040_create_courses_table',1);
INSERT INTO migrations VALUES(7,'2026_03_15_154041_create_course_goals_table',1);
INSERT INTO migrations VALUES(8,'2026_03_15_154042_create_course_sections_table',1);
INSERT INTO migrations VALUES(9,'2026_03_15_154043_create_certificates_table',1);
INSERT INTO migrations VALUES(10,'2026_03_15_154043_create_course_lectures_table',1);
INSERT INTO migrations VALUES(11,'2026_03_15_154043_create_course_resources_table',1);
INSERT INTO migrations VALUES(12,'2026_03_15_154043_create_orders_table',1);
INSERT INTO migrations VALUES(13,'2026_03_15_154043_create_tutor_schedules_table',1);
INSERT INTO migrations VALUES(14,'2026_03_15_154044_create_bookings_table',1);
INSERT INTO migrations VALUES(15,'2026_03_15_154044_create_payments_table',1);
INSERT INTO migrations VALUES(16,'2026_03_15_154045_create_course_progress_table',1);
INSERT INTO migrations VALUES(17,'2026_03_15_154045_create_enrollments_table',1);
INSERT INTO migrations VALUES(18,'2026_03_15_154046_create_reviews_table',1);
INSERT INTO migrations VALUES(19,'2026_04_05_170105_create_notifications_table',1);
INSERT INTO migrations VALUES(20,'2026_04_06_134138_create_quizzes_table',1);
INSERT INTO migrations VALUES(21,'2026_04_06_135309_create_quiz_questions_table',1);
INSERT INTO migrations VALUES(22,'2026_04_06_140035_create_quiz_options_table',1);
INSERT INTO migrations VALUES(23,'2026_04_06_141235_create_quiz_attempts_table',1);
INSERT INTO migrations VALUES(24,'2026_04_06_141518_create_quiz_answers_table',1);
INSERT INTO migrations VALUES(25,'2026_04_07_115106_create_articles_table',1);
INSERT INTO migrations VALUES(26,'2026_04_11_231441_add_bio_to_users_table',1);
INSERT INTO migrations VALUES(27,'2026_04_13_015545_create_site_settings_table',2);
INSERT INTO migrations VALUES(28,'2026_04_13_063838_make_order_id_nullable_in_enrollments_table',3);
INSERT INTO migrations VALUES(29,'2026_04_19_013926_add_scalev_product_id_to_courses_table',4);
INSERT INTO migrations VALUES(30,'2026_04_19_110907_add_type_to_quizzes_table',5);
INSERT INTO migrations VALUES(31,'2026_04_20_010335_create_pulse_tables',6);
INSERT INTO migrations VALUES(32,'2026_04_20_010500_create_activity_log_table',7);
