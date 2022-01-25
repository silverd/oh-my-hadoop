<?php

if (! function_exists('sshToNCDH')) {
    function sshToNCDH(array $config, $commands, array $options = [])
    {
        $ssh = \Spatie\Ssh\Ssh::create($config['user'], $config['host'], $config['port'] ?? null)
            ->disableStrictHostKeyChecking()
            ->usePrivateKey($config['prv_key']);

        foreach ($options as $option) {
            $ssh->addExtraOption($option);
        }

        $process = $ssh->execute($commands);

        $stderr = $process->getErrorOutput();
        $stdout = $process->getOutput();

        if (! $process->isSuccessful()) {
            throw new \Exception('SSH Failed - ' . $stderr);
        }

        return $stdout;
    }
}

if (! function_exists('sshToNCDHJson')) {
    function sshToNCDHJson(array $config, $commands, array $options = [])
    {
        $stdout = sshToNCDH($config, $commands, $options);

        $result = json_decode($stdout, true) ?: [];

        if (! isset($result['code']) || $result['code'] != 0) {
            throw new \Exception('SSH Error - ' . $stdout);
        }

        return $result;
    }
}
