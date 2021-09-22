/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  08/06/2021 10:48:31                      */
/*==============================================================*/


drop table if exists Image;

drop table if exists Joueur;

drop table if exists Question;

drop table if exists Reponse;

/*==============================================================*/
/* Table : Image                                                */
/*==============================================================*/
create table Image
(
   idImage              int not null,
   contenu              longblob not null,
   primary key (idImage)
);

/*==============================================================*/
/* Table : Joueur                                               */
/*==============================================================*/
create table Joueur
(
   idJoueur             int not null,
   pseudo               varchar(20) not null,
   motDePasse           varchar(25) not null,
   points               int,
   role                 int,
   primary key (idJoueur)
);

/*==============================================================*/
/* Table : Question                                             */
/*==============================================================*/
create table Question
(
   idQuestion           int not null,
   idImage              int,
   idJoueur             int not null,
   texte                varchar(50) not null,
   tempsQuestion        int not null,
   typeReponse          int not null,
   explication          varchar(100),
   niveau               int not null,
   isVerified           smallint not null,
   primary key (idQuestion)
);

/*==============================================================*/
/* Table : Reponse                                              */
/*==============================================================*/
create table Reponse
(
   idReponse            int not null,
   idQuestion           int not null,
   bonneReponse         smallint not null,
   texteReponse         varchar(50) not null,
   primary key (idReponse)
);

alter table Question add constraint FK_Contenir2 foreign key (idImage)
      references Image (idImage) on delete restrict on update restrict;

alter table Question add constraint FK_Identifier foreign key (idJoueur)
      references Joueur (idJoueur) on delete restrict on update restrict;

alter table Reponse add constraint FK_Posseder foreign key (idQuestion)
      references Question (idQuestion) on delete restrict on update restrict;

