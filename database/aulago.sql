create database aulago
character set utf8mb4
collate utf8mb4_unicode_ci;

use aulago;

create table roles (
    id_rol tinyint unsigned auto_increment primary key,
    nombre varchar(20) not null unique
) engine = InnoDB;

insert into roles (nombre)
values
    ('estudiante'),
    ('instructor'),
    ('administrador');

create table usuarios (
    id_usuario int unsigned auto_increment primary key,
    id_rol tinyint unsigned not null,
    nombre varchar(150) not null,
    genero enum('mujer', 'hombre', 'otro', 'prefiero_no_decir') not null,
    fecha_nacimiento date not null,
    avatar varchar(255) not null,
    email varchar(255) not null unique,
    contrasena_hash varchar(255) not null,
    intentos_fallidos tinyint unsigned not null default 0,
    bloqueado tinyint unsigned not null default 0,
    fecha_registro datetime not null default current_timestamp,
    fecha_actualizacion datetime not null default current_timestamp
        on update current_timestamp,
    constraint fk_usuarios_roles
        foreign key (id_rol) references roles (id_rol)
        on delete restrict
        on update cascade
) engine = InnoDB;


show tables;

select * from roles;

describe usuarios;

select * from usuarios;