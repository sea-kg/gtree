#pragma once

#include <string>
#include <json.hpp>
#include "HttpService.h"
// #include <employ_config.h>
// #include <employ_team_logos.h>
// #include <employ_flags.h>

class Ctf01dHttpServer {
    public:
        Ctf01dHttpServer();
        hv::HttpService *getService();
        int httpApiV1GetPaths(HttpRequest* req, HttpResponse* resp);
        int httpAdmin(HttpRequest* req, HttpResponse* resp);
        int httpWebFolder(HttpRequest* req, HttpResponse* resp);
        int httpApiV1Flag(HttpRequest* req, HttpResponse* resp);
        int httpApiV1Scoreboard(HttpRequest* req, HttpResponse* resp);

    private:
        std::string TAG;
        std::string m_sApiPathPrefix;
        std::string m_sTeamLogoPrefix;
        int m_nTeamLogoPrefixLength;
        hv::HttpService *m_pHttpService;

        EmployConfig *m_pConfig;
        EmployFlags *m_pEmployFlags;
        EmployDatabase *m_pEmployDatabase;
        EmployTeamLogos *m_pTeamLogos;

        std::string m_sIndexHtml;
        std::string m_sScoreboardHtmlFolder;

        nlohmann::json m_jsonGame;
        std::string m_sCacheResponseGameJson;
        nlohmann::json m_jsonTeams; // prepare data for list of teams
        std::string m_sCacheResponseTeamsJson;
};
