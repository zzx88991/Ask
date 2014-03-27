drop table if exists question;
create table question
(
    id         bigint not null auto_increment primary key,
    user_id   bigint not null,
    title      varchar(100) not null,
    date        date,
    text       varchar(2000) not null

);


drop table if exists user;
create table user
(
    id         bigint not null auto_increment primary key,
    username      varchar(50),
    password   varchar(100),
    gender     varchar(50) not null,
    description varchar(500) not null,
    email       varchar(100) not null,
    tags       varchar(5000),
    has_pic    varchar(50)
);

insert into user(username, password,gender,description,email,has_pic) values('admin', '52016a9964d0ac66c14b5aae6bb008df17d43d20','Male','This guys is the boss!','zzx88991@gmail.com','Yes') ;

    drop table if exists tag;
    create table tag
    (
        id         bigint not null auto_increment primary key,

        tag        varchar(50)
     );

drop table if exists question_tag;
create table question_tag
(
    id           bigint not null auto_increment primary key,
    question_id  bigint not null,
    tag_id       bigint not null
);


drop table if exists question_answer;

create table question_answer
(
    id           bigint not null auto_increment primary key,
    user_id      bigint not null,
    question_id  bigint not null,
    answer        varchar(5000) not null,
    rate         bigint not null,
    hide         varchar(3)
);

ALTER TABLE tag ENGINE = MYISAM;
ALTER TABLE question ENGINE = MYISAM 


