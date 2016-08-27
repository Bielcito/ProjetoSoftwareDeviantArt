CREATE TABLE IF NOT EXISTS category(
    codcategory SERIAL NOT NULL PRIMARY KEY,
    categoryname VARCHAR(150),
    categorypath VARCHAR(150)
);;

CREATE TABLE IF NOT EXISTS author(
    codauthor SERIAL NOT NULL PRIMARY KEY,
    userid VARCHAR(150),
    username VARCHAR(150),
    usericon VARCHAR(150),
    type VARCHAR(150)
);;

CREATE TABLE IF NOT EXISTS deviation(
    coddeviation SERIAL NOT NULL PRIMARY KEY,
    deviationid VARCHAR(150),
    printid VARCHAR(150),
    url VARCHAR(150),
    title VARCHAR(150),
    codcategory INTEGER references category(codcategory),
    isdownloadable BOOLEAN,
    ismature BOOLEAN,
    isfavourited BOOLEAN,
    isdeleted BOOLEAN,
    codauthor INTEGER references author(codauthor),
    codstats INTEGER references stats(codstats),
    publishedtime INTEGER,
    isallowcommenst BOOLEAN,
    codcontent INTEGER references content(codcontent)
);;

CREATE TABLE IF NOT EXISTS stats(
    codstats SERIAL NOT NULL PRIMARY KEY,
    comments INTEGER,
    favourites INTEGER
);;

CREATE TABLE IF NOT EXISTS content(
    codcontent SERIAL NOT NULL PRIMARY KEY,
    src VARCHAR(150),
    filesize INTEGER,
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
);;

CREATE TABLE IF NOT EXISTS thumb(
    codthumb SERIAL NOT NULL PRIMARY KEY,
    coddeviation INTEGER references deviation(coddeviation),
    src VARCHAR(150),
    height INTEGER,
    width INTEGER,
    istransparency BOOLEAN
)

