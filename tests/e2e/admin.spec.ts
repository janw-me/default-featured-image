import { test, expect } from "@playwright/test";
import { PHPRequestHandler, PHP } from "@php-wasm/universal";
import { runCLI } from "@wp-playground/cli";
// import { login } from "@wp-playground/blueprints";
import { readFileSync } from "fs";
import { resolve } from "path";

const dfiFileName = 'wp-content/uploads/2024/01/dfi.png';
const dfiFileNameThumb = 'wp-content/uploads/2024/01/dfi-150x150.png';

test.describe("DFI tests", () => {
	let cliServer: any;
	let handler: PHPRequestHandler;
	//   let php: PHP;

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
			quiet: false,
		});
		handler = cliServer.requestHandler;
		// php = await handler.getPrimaryPhp();
		// await login(php, {
		//   username: "admin",
		// });
	});

	test.afterEach(async () => {
		if (cliServer) {
			await cliServer.server.close();
		}
	});

	test("Check default setup.", async ({ page }) => {
		const wpAdminUrl = new URL(handler.absoluteUrl);

		/**
		 * Check the admin page. It should already have a DFI thanks to the blueprint.
		 */
		await page.goto(wpAdminUrl.toString() + "wp-admin/options-media.php");
		expect(page.locator('#preview-image img')).toBeVisible();
		expect( await page.locator('#preview-image img').getAttribute('src')).toMatch(wpAdminUrl.toString() + dfiFileNameThumb);
		const DFI_ID = await page.locator("#dfi_id").inputValue();
		expect(DFI_ID).toMatch(/^\d+$/);
		expect(DFI_ID).toBeTruthy();

		/**
		 * Check the homepage.
		 * It should have some posts with a DFI.
		 */

	});
});
