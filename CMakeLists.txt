cmake_minimum_required(VERSION 3.18)

project(gtree)

include(${CMAKE_CURRENT_SOURCE_DIR}/src.wsjcpp/CMakeLists.txt)

#### BEGIN_WSJCPP_APPEND
#### END_WSJCPP_APPEND

set(CMAKE_CXX_STANDARD 17)
set(EXECUTABLE_OUTPUT_PATH ${gtree_SOURCE_DIR})
# set(CMAKE_MODULE_PATH ${CMAKE_MODULE_PATH} "${CMAKE_SOURCE_DIR}/cmake/Modules/")

# Sources

# include header dirs
list (APPEND WSJCPP_INCLUDE_DIRS "src")
# list (APPEND WSJCPP_INCLUDE_DIRS "src/argument_processors")
# list (APPEND WSJCPP_INCLUDE_DIRS "src/checker")
# list (APPEND WSJCPP_INCLUDE_DIRS "src/scoreboard")
list (APPEND WSJCPP_INCLUDE_DIRS "src/employees")
# list (APPEND WSJCPP_INCLUDE_DIRS "src/store")
# list (APPEND WSJCPP_INCLUDE_DIRS "src/3rdparty/sqlite3/src")

# argument processors
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_check.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_clean.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_services.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_start.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_teams.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_version.cpp")
# list (APPEND WSJCPP_SOURCES "./src/argument_processors/argument_processor_web_test.cpp")

# employees
# list (APPEND WSJCPP_SOURCES "./src/employees/employ_team_logos.cpp")
# list (APPEND WSJCPP_SOURCES "./src/employees/employ_flags.cpp")
# list (APPEND WSJCPP_SOURCES "./src/employees/employ_scoreboard.cpp")
list (APPEND WSJCPP_SOURCES "./src/employees/employ_config.cpp")
# list (APPEND WSJCPP_SOURCES "./src/employees/employ_database.cpp")

# checker
# list (APPEND WSJCPP_SOURCES "src/checker/dorunchecker.cpp")
# list (APPEND WSJCPP_SOURCES "src/checker/service_checker_thread.cpp")

# http_server
list (APPEND WSJCPP_SOURCES "src/gtree_http_server.cpp")

# scoreboard
# list (APPEND WSJCPP_SOURCES "src/scoreboard/ctf01d_scoreboard.cpp")

# store
# list (APPEND WSJCPP_SOURCES "src/store/ctf01d_store.cpp")
# list (APPEND WSJCPP_SOURCES "src/store/http_downloader.cpp")

# main
list (APPEND WSJCPP_SOURCES "src/main.cpp")

# ZLIB
# find_package( ZLIB REQUIRED )
# if ( ZLIB_FOUND )
#     list (APPEND WSJCPP_INCLUDE_DIRS ${ZLIB_INCLUDE_DIRS})
#     include_directories( ${ZLIB_INCLUDE_DIRS} )
#     list (APPEND WSJCPP_LIBRARIES ${ZLIB_LIBRARIES})
# endif( ZLIB_FOUND )

# libhv
set(WITH_OPENSSL OFF CACHE BOOL "")
set(WITH_CURL OFF CACHE BOOL "")
# set(WITH_MQTT OFF CACHE BOOL "")
# set(WITH_EVPP OFF CACHE BOOL "")
set(BUILD_EXAMPLES OFF CACHE BOOL "")
# set(WITH_HTTP_CLIENT OFF CACHE BOOL "")
set(BUILD_SHARED OFF CACHE BOOL "")

add_subdirectory(src/3rdparty/libhv)
list (APPEND WSJCPP_LIBRARIES hv_static)
list (APPEND WSJCPP_INCLUDE_DIRS "${PROJECT_BINARY_DIR}/src/3rdparty/libhv/include/hv")

add_subdirectory(src/3rdparty/sqlite3)
# https://www.sqlite.org/threadsafe.html
# target_compile_definitions(sqlite3-static
#   PRIVATE
#     SQLITE_THREADSAFE=0
# )
list (APPEND WSJCPP_LIBRARIES sqlite3-static)
list (APPEND WSJCPP_INCLUDE_DIRS "${PROJECT_BINARY_DIR}/src/3rdparty/sqlite3/src")

include_directories(${WSJCPP_INCLUDE_DIRS})

add_executable (gtree ${WSJCPP_SOURCES})

target_link_libraries(gtree -lpthread ${WSJCPP_LIBRARIES} )

install(
    TARGETS
        gtree
    RUNTIME DESTINATION
        ${CMAKE_INSTALL_PREFIX}/bin
)

# enable testing functionality
# enable_testing()
# add_subdirectory(src/tests)
