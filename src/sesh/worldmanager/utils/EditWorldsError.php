<?php

namespace sesh\worldmanager\utils;

//No enums </3
class ManageWorldsError
{
    public static function Error(string $error, string $value)
    {
        return str_replace("{x}", $value, $error);
    }
    public const NOT_EMPTY = "Cannot unload world {x} while players are still in world.";
    public const NAME_EXISTS = "World already exists with name {x}.";
    public const INVALID_GENERATOR = "{x} is not a valid generator.";
    public const NOT_LOADED = "World {x} is not loaded, cannot unload.";
    public const ALREADY_LOADED = "World {x} is already loaded, cannot load";
    public const UNLOAD_DEFAULT = "Cannot unload default world.";
    public const INVALID_WORLD = "A world with the provided name {x} does not exist.";
    public const DIR_EXISTS = "Directory already exists with name {x}.";
}