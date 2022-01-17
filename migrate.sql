CreateUsersTable: create table `users` (`username` varchar(16) not null, `name` varchar(120) not null, `password` varchar(255) not null, `phone` varchar(15) not null, `type` enum('Owner', 'Tenant') not null default 'Tenant', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateUsersTable: alter table `users` add primary key `users_username_primary`(`username`)
CreateUsersTable: alter table `users` add unique `users_phone_unique`(`phone`)
CreatePasswordResetsTable: create table `password_resets` (`email` varchar(255) not null, `token` varchar(255) not null, `created_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreatePasswordResetsTable: alter table `password_resets` add index `password_resets_email_index`(`email`)
CreatePersonalAccessTokensTable: create table `personal_access_tokens` (`id` bigint unsigned not null auto_increment primary key, `tokenable_type` varchar(255) not null, `tokenable_id` bigint unsigned not null, `name` varchar(255) not null, `token` varchar(64) not null, `abilities` text null, `last_used_at` timestamp null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreatePersonalAccessTokensTable: alter table `personal_access_tokens` add index `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type`, `tokenable_id`)
CreatePersonalAccessTokensTable: alter table `personal_access_tokens` add unique `personal_access_tokens_token_unique`(`token`)
CreateKostsTable: create table `kosts` (`id` bigint unsigned not null auto_increment primary key, `user_username` char(36) not null, `address` varchar(100) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateKostsTable: alter table `kosts` add constraint `kosts_user_username_foreign` foreign key (`user_username`) references `users` (`username`)
CreateRoomTypesTable: create table `room_types` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(255) not null, `room_count` int not null, `kost_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateRoomTypesTable: alter table `room_types` add constraint `room_types_kost_id_foreign` foreign key (`kost_id`) references `kosts` (`id`) on delete cascade
CreateTenantsTable: create table `tenants` (`id` bigint unsigned not null auto_increment primary key, `user_username` char(36) not null, `entry_date` datetime default CURRENT_TIMESTAMP not null, `leave_date` datetime null, `due_date` datetime null, `status` tinyint(1) not null, `ktp` varchar(16) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateTenantsTable: alter table `tenants` add constraint `tenants_user_username_foreign` foreign key (`user_username`) references `users` (`username`)
CreateTenantsTable: alter table `tenants` add unique `tenants_ktp_unique`(`ktp`)
CreateRoomsTable: create table `rooms` (`id` bigint unsigned not null auto_increment primary key, `room_type_id` bigint unsigned not null, `tenant_id` bigint unsigned null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateRoomsTable: alter table `rooms` add constraint `rooms_room_type_id_foreign` foreign key (`room_type_id`) references `room_types` (`id`) on delete cascade
CreateRoomsTable: alter table `rooms` add constraint `rooms_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
CreateServicesTable: create table `services` (`id` bigint unsigned not null auto_increment primary key, `name` varchar(45) not null, `cost` int not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateKostImagesTable: create table `kost_images` (`id` bigint unsigned not null auto_increment primary key, `kost_id` bigint unsigned not null, `url` varchar(255) not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateKostImagesTable: alter table `kost_images` add constraint `kost_images_kost_id_foreign` foreign key (`kost_id`) references `kosts` (`id`)
CreateComplainsTable: create table `complains` (`id` bigint unsigned not null auto_increment primary key, `tenant_id` bigint unsigned not null, `description` text not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateComplainsTable: alter table `complains` add constraint `complains_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
CreateDendasTable: create table `dendas` (`id` bigint unsigned not null auto_increment primary key, `tenant_id` bigint unsigned not null, `title` varchar(255) not null, `description` text not null, `cost` int not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateDendasTable: alter table `dendas` add constraint `dendas_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
CreateNotificationsTable: create table `notifications` (`id` bigint unsigned not null auto_increment primary key, `tenant_id` bigint unsigned not null, `message` text not null, `is_read` tinyint(1) not null default '0', `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateNotificationsTable: alter table `notifications` add constraint `notifications_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
CreateInvoicesTable: create table `invoices` (`id` bigint unsigned not null auto_increment primary key, `tenant_id` bigint unsigned not null, `total` int not null, `type` enum('Pemasukan', 'Pengeluaran') not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateInvoicesTable: alter table `invoices` add constraint `invoices_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
CreateInvoiceDetailsTable: create table `invoice_details` (`id` bigint unsigned not null auto_increment primary key, `invoice_id` bigint unsigned not null, `description` varchar(255) null, `cost` int not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateInvoiceDetailsTable: alter table `invoice_details` add constraint `invoice_details_invoice_id_foreign` foreign key (`invoice_id`) references `invoices` (`id`) on delete cascade
CreateChatRoomsTable: create table `chat_rooms` (`id` bigint unsigned not null auto_increment primary key, `kost_id` bigint unsigned not null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateChatRoomsTable: alter table `chat_rooms` add constraint `chat_rooms_kost_id_foreign` foreign key (`kost_id`) references `kosts` (`id`) on delete cascade
CreateMessagesTable: create table `messages` (`id` bigint unsigned not null auto_increment primary key, `chat_room_id` bigint unsigned not null, `tenant_id` bigint unsigned not null, `message` varchar(256) null, `created_at` timestamp null, `updated_at` timestamp null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'
CreateMessagesTable: alter table `messages` add constraint `messages_chat_room_id_foreign` foreign key (`chat_room_id`) references `chat_rooms` (`id`) on delete cascade
CreateMessagesTable: alter table `messages` add constraint `messages_tenant_id_foreign` foreign key (`tenant_id`) references `tenants` (`id`) on delete cascade
