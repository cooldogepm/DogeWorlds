# DogeWorlds

A utility-first tool for worlds management

![DogeWorlds](assets/dogeworlds.png)

## Commands

| Command | Description | Usage | Permission |
| ------- | ----------- | ----- | ---------- |
| /dw create | Create a new world with generator and difficulty | `/dw create <world> <generator> [difficulty]` | `dogeworlds.subcommand.create` |
| /dw delete | Delete a world | `/dw delete <world>`  | `dogeworlds.subcommand.delete` |
| /dw list | List all loaded and unloaded worlds | `/dw list <world>`  | `dogeworlds.subcommand.list` |
| /dw load | Load an unloaded world | `/dw load <world>`  | `dogeworlds.subcommand.load` |
| /dw rename | Rename a world | `/dw rename <world> <new name> `  | `dogeworlds.subcommand.rename` |
| /dw teleport | Teleport players to different worlds | `/dw teleport <world> [player]`  | `dogeworlds.subcommand.teleport` |
| /dw unload | Unload a loaded world | `/dw unload <world> [force]`  | `dogeworlds.subcommand.unload` |

## World Generators

DogeWorlds supports all the registered world generators within the GeneratorManager and a custom built-in Void
generator.
