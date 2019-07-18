
CREATE TABLE `closing` (
  `table_name` varchar(128) NOT NULL COMMENT 'referred entity table name',
  `last_closing_time` datetime NOT NULL COMMENT 'Last closure time in that entity',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `closing`
  ADD PRIMARY KEY (`table_name`);
COMMIT;


CREATE TABLE `actionlog` (
  `actionlog_id` int(11) NOT NULL COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'User',
  `module` varchar(255) DEFAULT NULL COMMENT 'Module',
  `controller` varchar(512) NOT NULL COMMENT 'Controller',
  `action` varchar(255) NOT NULL COMMENT 'Action',
  `status` varchar(32) DEFAULT NULL COMMENT 'Status',
  `message` text DEFAULT NULL COMMENT 'Message',
  `class` varchar(255) DEFAULT NULL COMMENT 'Class',
  `model_id` int(11) DEFAULT NULL COMMENT 'Model id',
  `time` datetime NOT NULL COMMENT 'Time',
  `ip` varchar(128) NOT NULL COMMENT 'IP address',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `actionlog`
  ADD PRIMARY KEY (`actionlog_id`),
  ADD KEY `ix_user_id` (`user_id`),
  ADD KEY `ix_controller` (`controller`),
  ADD KEY `ix_module` (`module`),
  ADD KEY `ix_status` (`status`),
  ADD KEY `ix_model_id` (`model_id`);

ALTER TABLE `actionlog`
  MODIFY `actionlog_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';
COMMIT;







CREATE TABLE `survey` (
  `survey_id` int(11) NOT NULL COMMENT 'ID',
  `key` varchar(45) DEFAULT NULL COMMENT 'Survey key',
  `name` varchar(500) NOT NULL COMMENT 'Survey name',
  `options` text DEFAULT NULL COMMENT 'Survey options json',
  `status` varchar(255) DEFAULT NULL COMMENT 'Status',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `survey`
  ADD PRIMARY KEY (`survey_id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `ix_status` (`status`);

ALTER TABLE `survey`
  MODIFY `survey_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';



INSERT INTO `survey` (`survey_id`, `key`, `name`, `options`, `status`, `user_created`, `user_updated`, `user_closed`, `time_created`, `time_updated`, `time_closed`) VALUES
 ('1', '7507f1dc-8854-4b33-8860-87600b46aad3', 'tes5t-survey', '{}', 'active', '1', '1', NULL, '2018-07-25 00:00:00', '2018-07-25 00:00:00', '3000-12-31 00:00:00.000000');
COMMIT;

CREATE TABLE `survey_has_status` (
  `survey_has_status_id` int(11) NOT NULL COMMENT 'ID',
  `survey_id` int(11) NOT NULL COMMENT 'Survey id',
  `status` varchar(32) NOT NULL COMMENT 'Status',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `survey_has_status` (`survey_has_status_id`, `survey_id`, `status`, `user_created`, `user_updated`, `user_closed`, `time_created`, `time_updated`, `time_closed`) VALUES
(1, 1, 'created', 1000, 1000, NULL, '2018-05-17 08:27:44', '2018-05-17 08:27:44', '3000-12-31 00:00:00'),
(2, 1, 'active', 1000, 1000, NULL, '2018-05-17 08:27:44', '2018-05-17 08:27:44', '3000-12-31 00:00:00');

ALTER TABLE `survey_has_status`
  ADD PRIMARY KEY (`survey_has_status_id`),
  ADD KEY `fk_survey_has_status_survey_id` (`survey_id`),
  ADD KEY `ix_status` (`status`);

ALTER TABLE `survey_has_status`
  MODIFY `survey_has_status_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

ALTER TABLE `survey_has_status`
  ADD CONSTRAINT `fk_survey_has_status_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`);
COMMIT;



CREATE TABLE `respondent` (
  `respondent_id` int(11) NOT NULL COMMENT 'ID',
  `key` varchar(32) DEFAULT NULL,
  `survey_id` int(11) DEFAULT NULL COMMENT 'Survey',
  `token` varchar(128) DEFAULT NULL COMMENT 'Token',
  `email_address` varchar(255) DEFAULT NULL COMMENT 'email address to send email to',
  `alternative_email_addresses` text DEFAULT NULL COMMENT 'Alternative email addresses',
  `phone_number` varchar(64) DEFAULT NULL COMMENT 'Phone number',
  `alternative_phone_numbers` text DEFAULT NULL COMMENT 'Alternative phone numbers',
  `time_collector_registered` datetime DEFAULT NULL COMMENT 'Time Registration in Collector',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




ALTER TABLE `respondent`
  ADD PRIMARY KEY (`respondent_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `fk_respondent_survey_id` (`survey_id`),
  ADD KEY `ix_token` (`token`),
  ADD KEY `ix_email_address` (`email_address`),
  ADD KEY `ix_phone_number` (`phone_number`);


ALTER TABLE `respondent`
  MODIFY `respondent_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=4;

ALTER TABLE `respondent`
  ADD CONSTRAINT `fk_respondent_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`);

INSERT INTO `respondent` (`respondent_id`,`key`, `survey_id`, `token`, `email_address`, `alternative_email_addresses`, `phone_number`, `alternative_phone_numbers`,  `time_collector_registered`, `user_created`, `user_updated`, `user_closed`, `time_created`, `time_updated`, `time_closed`) VALUES
(1, 'yks', 1, 'c9723dcc-daed-4078-b373-cbe173c03740', 'tonis@andmemasin.eu', NULL, '1234567 1', NULL, null, 1, 1, NULL, '2018-05-17 13:34:27', '2018-05-17 13:34:27', '3000-12-31 00:00:00'),
(2, 'kaks', 1, 'df56bf0a-c9b4-4cc2-8458-d17e22a0863d', 'rejected@example.com', NULL, '1234567 2', NULL, null, 1, 1, NULL, '2018-05-17 13:34:27', '2018-05-17 13:34:27', '3000-12-31 00:00:00'),
(3, 'kolm', 1, 'a1b24b34-4a21-4c4a-a6e0-09d2ae48c949', 'not-rejected@example.com', NULL, '1234567 3', NULL, null, 1, 1, NULL, '2018-05-17 13:34:27', '2018-05-17 13:34:27', '3000-12-31 00:00:00');
COMMIT;



CREATE TABLE `rating` (
  `rating_id` int(11) NOT NULL,
  `respondent_id` int(11) NOT NULL COMMENT 'Respondent',
  `survey_id` int(11) NOT NULL COMMENT 'Survey',
  `sample_id` int(11) NOT NULL COMMENT 'Sample',
  `value` int(11) NOT NULL COMMENT 'Rating value',
  `comment` text DEFAULT NULL COMMENT 'Comment',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `rating` (`rating_id`, `respondent_id`, `survey_id`, `sample_id`, `value`, `comment`, `user_created`, `user_updated`, `user_closed`, `time_created`, `time_updated`, `time_closed`) VALUES
(1, 1, 1, 1, 1, 'sdfadsf', 1, 1, NULL, '2018-05-17 16:04:20', '2018-05-17 16:04:20', '3000-12-31 00:00:00');

--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `ix_value` (`value`);

ALTER TABLE `rating`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `rating`
  ADD CONSTRAINT `fk_rating_respondent_id` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`respondent_id`),
  ADD CONSTRAINT `fk_rating_sample_id` FOREIGN KEY (`sample_id`) REFERENCES `sample` (`sample_id`),
  ADD CONSTRAINT `fk_rating_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`);
COMMIT;



CREATE TABLE `rejection` (
  `rejection_id` int(11) NOT NULL COMMENT 'ID',
  `survey_id` int(11) DEFAULT NULL COMMENT 'Survey',
  `respondent_id` int(11) DEFAULT NULL COMMENT 'Respondent',
  `email_address` varchar(255) NOT NULL COMMENT 'email address to send email to',
  `type` varchar(45) DEFAULT NULL COMMENT 'Bounce type',
  `bounce` text DEFAULT NULL COMMENT 'Sisimai bounce object as JSON',
  `time_rejected` datetime NOT NULL COMMENT 'Rejection time',
  `user_created` int(11) NOT NULL DEFAULT 1 COMMENT 'Created by',
  `user_updated` int(11) NOT NULL DEFAULT 1 COMMENT 'Updated by',
  `user_closed` int(11) DEFAULT NULL COMMENT 'Closed by',
  `time_created` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Created at',
  `time_updated` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Updated at',
  `time_closed` datetime NOT NULL DEFAULT '3000-12-31 00:00:00' COMMENT 'Closed at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `rejection`
  ADD PRIMARY KEY (`rejection_id`),
  ADD KEY `fk_rejection_survey_id` (`survey_id`),
  ADD KEY `fk_rejection_respondent_id` (`respondent_id`),
  ADD KEY `ix_email_address` (`email_address`);

ALTER TABLE `rejection`
  MODIFY `rejection_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

ALTER TABLE `rejection`
  ADD CONSTRAINT `fk_rejection_respondent_id` FOREIGN KEY (`respondent_id`) REFERENCES `respondent` (`respondent_id`),
  ADD CONSTRAINT `fk_rejection_survey_id` FOREIGN KEY (`survey_id`) REFERENCES `survey` (`survey_id`);
COMMIT;

INSERT INTO `rejection` (`rejection_id`, `survey_id`, `respondent_id`, `email_address`, `type`, `bounce`, `time_rejected`) VALUES ('1', '1', '1', 'me@example.com', 'complaint', NULL, '2018-07-25 00:00:00');
INSERT INTO `rejection` (`rejection_id`, `survey_id`, `respondent_id`, `email_address`, `type`, `bounce`, `time_rejected`) VALUES ('2', '1', '1', 'me2@example.com', 'hard', 'bounce data here', '2018-07-25 00:00:00');
INSERT INTO `rejection` (`rejection_id`, `survey_id`, `respondent_id`, `email_address`, `type`, `bounce`, `time_rejected`) VALUES ('3', '1', '1', 'me3@example.com', 'hard', 'bounce data here', '2018-07-25 00:00:00');
INSERT INTO `rejection` (`rejection_id`, `survey_id`, `respondent_id`, `email_address`, `type`, `bounce`, `time_rejected`) VALUES ('4', null, null, 'rejected@example.com', 'hard', NULL, '2018-07-25 00:00:00');
