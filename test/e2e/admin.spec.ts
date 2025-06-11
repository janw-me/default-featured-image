import { test, expect } from "@playwright/test";
import { PHPRequestHandler, PHP } from "@php-wasm/universal";
import { runCLI } from "@wp-playground/cli";
import { login } from "@wp-playground/blueprints";
import { readFileSync } from "fs";
import { resolve } from "path";

test.describe("Workshop Tests", () => {
  let cliServer: any;
  let handler: PHPRequestHandler;
  let php: PHP;

  test.beforeEach(async () => {
    const blueprint = JSON.parse(
      readFileSync(resolve(".wordpress-org/blueprints/blueprint.json"), "utf8")
    );
    cliServer = await runCLI({
      command: "server",
      mount: [
        {
          hostPath: "./",
          vfsPath: "/wordpress/wp-content/plugins/default-featured-image",
        },
      ],
      blueprint,
      quiet: true,
    });
    handler = cliServer.requestHandler;
    php = await handler.getPrimaryPhp();
    await login(php, {
      username: "admin",
    });
  });

  test.afterEach(async () => {
    if (cliServer) {
      await cliServer.server.close();
    }
  });

  test("Admin page form", async ({ page }) => {
    const wpAdminUrl = new URL(handler.absoluteUrl);
    wpAdminUrl.pathname = "wp-admin/options-media.php";
    wpAdminUrl.searchParams.set("page", "workshop-tests");
    await page.goto(wpAdminUrl.toString());

    await expect(page).toHaveTitle(/Media Settings/);
  });
});
