<?php

namespace PHPSTORM_META {
    // Command methods
    override(\Illuminate\Console\Command::info(), type(0));
    override(\Illuminate\Console\Command::warn(), type(0));
    override(\Illuminate\Console\Command::line(), type(0));
    override(\Illuminate\Console\Command::option(), type(0));

    // Helper methods
    override(database_path(), map([
        '' => '@',
    ]));
    override(base_path(), map([
        '' => '@',
    ]));

    // Str methods
    override(\Illuminate\Support\Str::contains(), map([
        0 => '@',
    ]));
}
