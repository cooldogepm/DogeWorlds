<?php

declare(strict_types=1);

namespace DogeDev\DogeWorlds\language;

final class Messages
{
    public const MESSAGE_CATEGORY_REGULAR = 0;
    public const MESSAGE_CATEGORY_ERROR = 1;
    public const MESSAGE_CATEGORY_USAGE = 2;
    public const MESSAGE_CATEGORY_ALIAS = 3;

    public const ERROR_NO_ENOUGH_PRIVILEGES = "noEnoughPrivileges";
    public const ERROR_PLAYER_IS_OFFLINE = "playerIsOffline";
    public const ERROR_WORLD_NOT_FOUND = "worldNotFound";
    public const ERROR_WORLD_GENERATOR_INVALID = "worldGeneratorInvalid";
    public const ERROR_WORLD_FORMAT_UNSUPPORTED = "worldFormatUnsupported";
    public const ERROR_WORLD_NAME_TAKEN = "worldGeneratorInvalid";
    public const ERROR_WORLD_CANNOT_DELETE_DEFAULT = "worldCannotDeleteDefault";
    public const ERROR_WORLD_ALREADY_LOADED = "worldAlreadyLoaded";
    public const ERROR_WORLD_LOADING_FAILED = "worldLoadingFailed";
    public const ERROR_WORLD_IS_NOT_LOADED = "worldIsNotLoaded";
    public const ERROR_WORLD_TELEPORT_FAIL = "worldTeleportFail";
    public const ERROR_WORLD_RENAME_FAIL = "worldRenameFail";

    public const MESSAGE_WORLD_CREATED = "worldSuccessfullyCreated";
    public const MESSAGE_WORLD_DELETED = "worldSuccessfullyDeleted";
    public const MESSAGE_WORLD_LOADED = "worldSuccessfullyLoaded";
    public const MESSAGE_WORLD_RENAMED = "worldSuccessfullyRenamed";
    public const MESSAGE_WORLD_UNLOADED = "worldSuccessfullyUnloaded";
    public const MESSAGE_WORLD_TELEPORTED = "worldSuccessfullyTeleported";
    public const MESSAGE_WORLD_LIST = "worldsList";

    public const ALIAS_COMMAND_DEFAULT = "default";
    public const ALIAS_SUBCOMMAND_CREATE = "create";
    public const ALIAS_SUBCOMMAND_DELETE = "delete";
    public const ALIAS_SUBCOMMAND_LIST = "list";
    public const ALIAS_SUBCOMMAND_LOAD = "load";
    public const ALIAS_SUBCOMMAND_RENAME = "rename";
    public const ALIAS_SUBCOMMAND_TELEPORT = "teleport";
    public const ALIAS_SUBCOMMAND_UNLOAD = "unload";

    public const USAGE_COMMAND_DEFAULT = "default";
    public const USAGE_SUBCOMMAND_CREATE = "create";
    public const USAGE_SUBCOMMAND_DELETE = "delete";
    public const USAGE_SUBCOMMAND_LOAD = "load";
    public const USAGE_SUBCOMMAND_RENAME = "rename";
    public const USAGE_SUBCOMMAND_TELEPORT = "teleport";
    public const USAGE_SUBCOMMAND_TELEPORT_CONSOLE = "teleportConsole";
    public const USAGE_SUBCOMMAND_UNLOAD = "unload";
}
