-- #! sqlite
-- #{ players
-- #    { init
CREATE TABLE IF NOT EXISTS PLAYERS(uuid VARCHAR(255) PRIMARY KEY, rules int, class int, kit int, last_logged int, online_time int, cooldowns text, tokens int, skills text, skill_resets int);
-- #    }
-- #    { insert
-- # 	  :uuid string
-- #      :rules int
-- #      :class int
-- #      :kit int
-- #      :last_logged int
-- #      :online_time int
-- #      :cooldowns string
-- #      :tokens int
-- #      :skills string
-- #      :skill_resets int
INSERT INTO PLAYERS(
    uuid,
    rules,
    class,
    kit,
    last_logged,
    online_time,
    cooldowns,
    tokens,
    skills,
    skill_resets
) VALUES (
             :uuid,
             :rules,
             :class,
             :kit,
             :last_logged,
             :online_time,
             :cooldowns,
             :tokens,
             :skills,
             :skill_resets
         );
-- #    }
-- #    { update_lastlogged
-- # 	  :uuid string
-- #      :last_logged int
UPDATE PLAYERS SET last_logged = :last_logged where uuid = :uuid;
-- #    }
-- #    { update_tokens
-- # 	  :uuid string
-- #      :tokens int
UPDATE PLAYERS SET tokens = :tokens where uuid = :uuid;
-- #    }
-- #    { isregistered
-- # 	  :uuid string
SELECT * FROM PLAYERS
WHERE uuid = :uuid;
-- #    }
-- #}
-- #{ koths
-- #    { init
CREATE TABLE IF NOT EXISTS KOTHS(x1 int, z1 int, x2 int, z2 int, centreY int);
-- #    }
-- #    { add
-- # 	  :x1 int
-- # 	  :z1 int
-- # 	  :x2 int
-- # 	  :z2 int
-- # 	  :centreY int
INSERT INTO KOTHS(
    x1,
    z1,
    x2,
    z2,
    centreY
) VALUES (
             :x1,
             :z1,
             :x2,
             :z2,
             :centreY
         );
-- #    }
-- #    { select
SELECT * FROM KOTHS;
-- #    }
-- #}
-- #{ leaderboards
-- #    { init
CREATE TABLE IF NOT EXISTS LEADERBOARDS(NAME TEXT, TYPE INT, X FLOAT, Y FLOAT, Z FLOAT, WORLD TEXT);
-- #    }
-- #    { insert
-- # 	  :name string
-- # 	  :type int
-- # 	  :x float
-- # 	  :y float
-- # 	  :z float
-- # 	  :world string
INSERT INTO LEADERBOARDS(NAME, TYPE, X, Y, Z, WORLD) VALUES(:name, :type, :x, :y, :z, :world);
-- #    }
-- #    { select
SELECT * FROM LEADERBOARDS;
-- #    }
-- #}